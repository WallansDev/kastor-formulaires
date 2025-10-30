<?php

// ************************
// *    Timothé VAQUIÉ    *
// *    Version : 1.0     *
// ************************

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class MailerFormulaireYeastar extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        if ($this->data['urlPbx'] == "") {
            $pbx = "";
        } else {
            $pbx = "| https://".$this->data['urlPbx'] . ".vokalise.fr";
        }
        
        $email = $this->subject('[YEASTAR] Nouveau IPBX - ' . $this->data['customer_name'] . $pbx)
            ->view('emails.yeastar')
            ->with([
                'reseller_name' => $this->data['reseller_name'],
                'reseller_email' => $this->data['reseller_email'],
                'customer_name' => $this->data['customer_name'],
                'urlPbx' => $this->data['urlPbx'],
                'portes' => $this->data['portes'],
                'extensions' => $this->data['extensions'],
                'callGroups' => $this->data['callGroups'],
                'queues' => $this->data['queues'],
                'timetable_ho' => $this->data['timetable_ho'],
                'svi' => $this->data['svi'],
                'dialplan' => $this->data['dialplan'],
                'infos_remarques' => $this->data['infos_remarques'],
                // 'devices' => $this->data['devices'],
            ]);

        if (isset($this->data['pdf'])) {
            $email->attachData($this->data['pdf'], 'dossier_parametrage_'.str_replace(' ', '_', strtolower($this->data['customer_name'])).'.pdf', [
                'mime' => 'application/pdf',
            ]);
        }

        
        return $email;
    }
}
