<?php

// ************************
// *    Timothé VAQUIÉ    *
// *    Version : 1.0     *
// ************************

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailerFormulaire extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        $email = $this->subject("Nouveau IPBX - ". $this->data['customer_name'] . " | https://" . $this->data['urlPbx']. ".wildixin.com")
            ->view('emails.contact')
            ->with([
                'reseller_name' => $this->data['reseller_name'],
                'customer_name' => $this->data['customer_name'],
                'urlPbx' => $this->data['urlPbx'],
                'portes' => $this->data['portes'],
                'extensions' => $this->data['extensions'],
                'callGroups' => $this->data['callGroups'],
                'timetable_ho' => $this->data['timetable'],
                'svi_options' => $this->data['svi_options'],
                'dialplan' => $this->data['dialplan'],
                'infos_remarques' => $this->data['infos_remarques'],
            ]);

        // Si un fichier est défini et existe
        if (isset($this->data['fichier']) && file_exists($this->data['fichier'])) {

            // Renommer le fichier à la volée si nécessaire
            // $newFilename = 'export_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

            $email->attach($this->data['fichier'], [
                
                'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // type MIME
            ]);
        }

        return $email;
    }
}
