<?php

namespace Database\Factories;

use App\Models\Note;
use App\Models\Photo;
use App\Support\UploadBase64Trait;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class NoteFactory extends Factory
{
    use UploadBase64Trait;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Note::class;

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (Note $note) {
            //
        })->afterCreating(function (Note $note) {
            $aAttachements = [];
            $attachements = Storage::disk('dev')->files('base64');
            foreach ($attachements as $attachement) {
                $pieces_jointe = [
                    'file_name' => basename($attachement),
                    'url' => Storage::disk('dev')->get($attachement),
                ];
                $filename =  $this->uploadFile($pieces_jointe, 'demandes');
                $pj = Photo::create([
                    'url' => $filename,
                ]);
                $aAttachements[] = $pj;
            }
            $note->attachements()->saveMany($aAttachements);
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'note' => 'Tous les générateurs de Lorem Ipsum sur Internet tendent à reproduire le même extrait sans fin, ce qui fait de lipsum.com le seul vrai générateur de Lorem Ipsum',
            'report_to_soumission' => Arr::random([true, false]),
            'report_to_contrat' => Arr::random([true, false]),
            'report_to_bon_de_travail' => Arr::random([true, false]),
            'report_to_facture' => Arr::random([true, false]),
        ];
    }
}
