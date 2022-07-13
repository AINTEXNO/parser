<?php

namespace App\Console\Commands;

use App\Models\AutoModel;
use App\Models\BodyType;
use App\Models\Color;
use App\Models\EngineType;
use App\Models\GearType;
use App\Models\Generation;
use App\Models\Mark;
use App\Models\Offer;
use App\Models\Transmission;
use App\Models\Year;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class RunParser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parser:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the parser and specify the path to the file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return void
     */
    public function handle()
    {
        $path = $this->ask('Specify the path to the upload file');

        $this->info('The command was successful!');

        try {
            $file = File::get(public_path($path));
        }
        catch (\Exception $exception) {
            $file = File::get(public_path('/files/data_light.xml'));
        }

        $this->line('File parse successful!');

        $xml = simplexml_load_string($file);

        $json = json_encode($xml);
        $arr = json_decode($json, true);

        if(!Schema::hasTable('offers')) {
            Artisan::call('migrate:refresh');
            $this->info('Database tables created successfully!');
        }

        $offers = $arr['offers']['offer'];
        $offersCollect = collect($arr['offers']['offer']);
        $count = Offer::count();
        $allOffers = Offer::all();

        if($count > 0) {
            $offersId = $offersCollect->map(function($item) {
                return $item['id'];
            });

            // Удаление записей из БД, которых нет в xml-файле
            $allOffers->each(function($item) use ($offersId) {
                if(!in_array($item->id, $offersId->toArray())) {
                    $item->delete();
                }
            });

            $this->line('Undiscovered entries deleted');
            $this->info('Database updated successfully!');
        }

        // Обновление и добавление записей в БД из xml-файла
        foreach ($offers as $offer) {
            $offerModel = Offer::find($offer['id']) ?? new Offer();

            $offerModel->id = $offer['id'];
            $offerModel->mark_id = Mark::firstOrCreate(['mark' => $offer['mark']])->id;
            $offerModel->auto_model_id = AutoModel::firstOrCreate(['model' => $offer['model']])->id;
            $offerModel->year_id = Year::firstOrCreate(['year' => $offer['year']])->id;
            $offerModel->color_id = Color::firstOrCreate(['color' => $offer['color']])->id;
            $offerModel->body_type_id = BodyType::firstOrCreate(['body_type' => $offer['body-type']])->id;
            $offerModel->engine_type_id = EngineType::firstOrCreate(['engine_type' => $offer['engine-type']])->id;
            $offerModel->transmission_id = Transmission::firstOrCreate(['transmission' => $offer['transmission']])->id;
            $offerModel->gear_type_id = GearType::firstOrCreate(['gear_type' => $offer['gear-type']])->id;
            $offerModel->generation_id = Generation::firstOrCreate(['generation' => $offer['generation']])->id;
            $offerModel->run = $offer['run'];

            $offerModel->save();
        }

        $this->info('New entries has been added successfully!');
    }
}
