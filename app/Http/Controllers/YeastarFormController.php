<?php

// ************************
// *    Timothé VAQUIÉ    *
// *    Version : 3.0     *
// ************************

namespace App\Http\Controllers;

use App\Mail\MailerFormulaireyeastar ;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Rules\ValidExtensionYeastar;

class YeastarFormController extends Controller
{
    // IPBX
    public function generalInfo()
    {
        $data = Session::get('form_yeastar', []);
        return view('yeastar.general_info', compact('data'));
    }

    public function postGeneralInfo(Request $request)
    {
        $validated = $request->validate([
            'reseller_name' => 'required|string',
            'reseller_email' => 'required|string',
            'customer_name' => 'required|string',
            'url_pbx' => 'nullable|string',
        ]);

        Session::put('form_yeastar.reseller_name', $validated['reseller_name']);
        Session::put('form_yeastar.reseller_email', $validated['reseller_email']);
        Session::put('form_yeastar.customer_name', $validated['customer_name']);
        Session::put('form_yeastar.url_pbx', str_replace(' ', '', strtolower($validated['url_pbx'])));

        return redirect()->route('yeastar.num_list');
    }

    // numList
    public function numList()
    {
        $dataForm = Session::get('form_yeastar', []);
        $data['portes'] = session('form_yeastar.numeros.portes', []);

        if (!session('form_yeastar.customer_name')) {
            return redirect()->route('yeastar.general_info')->with('error', 'Des informations sont manquantes pour continuer.');
        }
        return view('yeastar.num-list', compact('data', 'dataForm'));
    }

    public function postNumList(Request $request)
    {
        $action = $request->input('action_type');
        $portes = session()->get('form_yeastar.numeros.portes', []);

        // Ajouter un numéro porté
        if ($action === 'ajouter_porte') {
            $validated = $request->validate(
                [
                    'numero_porte' => ['required', 'string', 'regex:/^\+33(1|2|3|4|5|8|9)\d{8}$/'],
                ],
                [
                    'numero_porte.required' => 'Le numéro porté est obligatoire.',
                    'numero_porte.regex' => 'Le numéro porté doit être au format +33 suivi de 9 chiffres (+330, +336, +337 interdit !).',
                ],
            );

            $portes = session()->get('form_yeastar.numeros.portes', []);

            // Vérification unicité dans la session
            foreach ($portes as $porte) {
                if ($porte['numero'] === $validated['numero_porte']) {
                    return back()
                        ->withErrors(['numero_porte' => 'Ce numéro est déjà dans la liste.'])
                        ->withInput();
                }
            }

            $portes[] = [
                'numero' => $validated['numero_porte'],
                'provisoire' => null,
            ];

            session()->put('form_yeastar.numeros.portes', $portes);
        }

        // Ajouter un numéro provisoire
        elseif ($action === 'ajouter_provisoire') {
            $validated = $request->validate(
                [
                    'numero_provisoire' => ['required', 'regex:/^\+33(1|2|3|4|5|8|9)\d{8}$/'],
                    'porte_selectionne' => ['required', 'string'],
                ],
                [
                    'numero_provisoire.required' => 'Le numéro provisoire est obligatoire.',
                    'numero_provisoire.regex' => 'Le numéro provisoire doit être au format +33 suivi de 9 chiffres (+330, +336, +337 interdit !).',
                    'porte_selectionne.required' => 'Vous devez sélectionner un numéro porté existant.',
                ],
            );

            $portes = session()->get('form_yeastar.numeros.portes', []);

            // Vérifier si ce numéro existe déjà dans les numéros portés ou en tant que provisoire
            $portes_exists = collect($portes)->pluck('numero')->contains($validated['numero_provisoire']) || collect($portes)->pluck('provisoire')->contains($validated['numero_provisoire']);

            if ($portes_exists) {
                return back()
                    ->withErrors(['numero_provisoire' => 'Ce numéro est déjà inscrit comme numéro porté/provisoire.'])
                    ->withInput();
            }

            foreach ($portes as &$porte) {
                if ($porte['numero'] === $validated['porte_selectionne']) {
                    $porte['provisoire'] = $validated['numero_provisoire'];
                    break;
                }
            }

            session()->put('form_yeastar.numeros.portes', $portes);
        }

        // Suppression numéro porté ou provisoire
        elseif (str_starts_with($action, 'supprimer_porte_')) {
            $index = (int) str_replace('supprimer_porte_', '', $action);
            if (isset($portes[$index])) {
                array_splice($portes, $index, 1);
            }
        } elseif (str_starts_with($action, 'supprimer_provisoire_')) {
            $index = (int) str_replace('supprimer_provisoire_', '', $action);
            if (isset($portes[$index])) {
                $portes[$index]['provisoire'] = null;
            }
        }

        session()->put('form_yeastar.numeros.portes', $portes);
        return back()->withInput();
    }

    // Extensions

    public function extension()
    {
        $data = Session::get('form', []);

        $portes = session('form_yeastar.numeros.portes', []);

        if (empty($portes)) {
            return redirect()
                ->route('yeastar.num_list')
                ->with([
                    'error' => 'Vous devez au minimum renseigner un numéro porté/créé.',
                ]);
        }

        return view('yeastar.extension', ['data' => ['portes' => $portes]]);
    }

    public function postExtension(Request $request)
    {
        $extensions = $request->input('extensions', []);

        if ($request->has('delete')) {
            $indexToDelete = $request->input('delete');

            // Supprime l'élément de la liste
            unset($extensions[$indexToDelete]);

            // Réindexe le tableau
            $extensions = array_values($extensions);

            // Sauvegarde en session
            session()->put('form_yeastar.extensions', $extensions);

            return redirect()->back()->with('success', 'Extension supprimée.');
        }

        $validated = $request->validate([
            'extensions' => 'required|array|min:1',
            'extensions.*.extension' => ['required', new ValidExtensionYeastar($extensions)],
            'extensions.*.name' => 'required|string',
            'extensions.*.email' => 'nullable|email',
            'extensions.*.numPorte' => 'required|string',
            'extensions.*.language' => 'required|string|in:fr,en,it,es',
            // 'extensions.*.licence' => 'required|string|in:service,basic,essential,business,premium',
        ]);

        session()->put('form_yeastar.extensions', $validated['extensions']);

        return redirect()->route('yeastar.call_group')->with('success', 'Extensions sauvegardées en mémoire (session) !');
    }

    // Devices
    // public function devices()
    // {
    //     $data = Session::get('form_yeastar');

    //     if (!session('form_yeastar.extensions')) {
    //         return back()->with('error', 'Au moins une extension est obligatoire pour continuer.');
    //     }

    //     return view('yeastar.devices', compact('data'));
    // }

    // public function postDevices(Request $request)
    // {
    //     $devices = session('form_yeastar.devices');

    //     if ($request->action_type === 'add_device') {
    //         $validated = $request->validate([
    //             'device_name' => 'required|string',
    //             'extension' => 'nullable|string',
    //         ]);

    //         $form = session('form_yeastar', []);

    //         $form['devices'][] = [
    //             'device_name' => $validated['device_name'],
    //             'extension' => $validated['extension'] ?? null,
    //         ];

    //         if (isset($validated['extension'])) {
    //             $extensionData = collect($form['extensions'])->firstWhere('extension', $validated['extension']);

    //             if ($extensionData) {
    //                 $licence = strtolower($extensionData['licence']);

    //                 $userName = $extensionData['name'] ?? null;

    //                 $userExtensions = collect($form['extensions'])->filter(fn($ext) => $ext['name'] === $userName)->pluck('extension')->toArray();

    //                 if ($licence === 'basic') {
    //                     $deviceCount = collect($form['devices'])->filter(fn($device) => $device['extension'] && in_array($device['extension'], $userExtensions))->count();

    //                     if ($deviceCount > 1) {
    //                         return redirect()
    //                             ->back()
    //                             ->with('error', "L'extension \"" . $validated['extension'] . ' - ' . $userName . "\" ayant une licence BASIC ne peut avoir qu’un seul équipement.");
    //                     }
    //                 } elseif ($licence === 'service') {
    //                     return redirect()
    //                         ->back()
    //                         ->with('error', "L'extension \"" . $validated['extension'] . ' - ' . $userName . "\" ayant une licence SERVICE ne peut pas être lié à un équipement.");
    //                 }
    //             }
    //         }
    //         session(['form_yeastar' => $form]);

    //         return redirect()->back()->with('success', 'Équipement ajouté.');
    //     }

    //     if (str_starts_with($request->action_type, 'delete_device_')) {
    //         $index = (int) str_replace('delete_device_', '', $request->action_type);
    //         unset($devices[$index]);
    //         $devices = array_values($devices);

    //         session(['form_yeastar.devices' => $devices]);

    //         return redirect()->back()->with('success', 'Équipement retiré.');
    //     }

    //     // $deviceNames = ['W-AIR SYNC PLUS BASE', 'W-AIR SYNC PLUS BASE OUTDOOR', 'W-AIR SMALL BUSINESS'];
    //     // $containsSpecialDevice = collect($devices)->contains(function ($device) use ($deviceNames) {
    //     //     return in_array($device['device_name'], $deviceNames);
    //     // });

    //     // if ($containsSpecialDevice) {
    //     //     return redirect()->route('form.dect');
    //     // } else {
    //         return redirect()->route('yeastar.call_group');
    //     // }
    // }

    // Devices Phones
    // public function dect()
    // {
    //     $data = Session::get('form');

    //     if (!session('form_yeastar.extensions')) {
    //         return back()->with('error', 'Au moins une extension est obligatoire pour continuer.');
    //     }

    //     return view('yeastar.dect', compact('data'));
    // }

    // public function postDect(Request $request) {}

    // CALLGROUPS
    public function callGroup()
    {
        $extensions = session('form_yeastar.extensions', []);
        $callGroups = session('form_yeastar.callgroups', []);
        $queues = session('form_yeastar.queues', []);

        // if (!session('form_yeastar.extensions')) {
        //     return back()->with('error', 'Au moins une extension est obligatoire pour continuer.');
        // }

        return view('yeastar.call_group', compact('extensions', 'callGroups', 'queues'));
    }

    public function postCallGroup(Request $request)
    {
        $callGroups = session('form_yeastar.callgroups', []);
        $queues = session('form_yeastar.queues', []);

        if ($request->action_type === 'add_group') {
            $request->validate([
                'cgName' => 'required|string',
                'cg_type' => 'required|string',
            ]);

            $callGroups[] = [
                'name' => $request->cgName,
                'type' => $request->cg_type,
                'ext' => [],
            ];
        }

        if ($request->action_type === 'add_queue') {
            $request->validate([
                'qName' => 'required|string',
            ]);

            $queues[] = [
                'name' => $request->qName,
                'ext' => [],
            ];
        }

        if ($request->action_type === 'add_ext') {
            $groupName = $request->input('cg_selectionne');
            $extension = $request->input('ext_selectionne');
            $foundInCallGroup = false;

            // Chercher dans les call groups
            foreach ($callGroups as &$group) {
                if ($group['name'] === $groupName && $extension && !in_array($extension, $group['ext'])) {
                    $group['ext'][] = $extension;
                    $foundInCallGroup = true;
                    break;
                }
            }
            unset($group); // bonne pratique avec les références

            // Si pas trouvé dans les call groups, chercher dans les queues
            if (!$foundInCallGroup) {
                foreach ($queues as &$queue) {
                    if ($queue['name'] === $groupName && $extension && !in_array($extension, $queue['ext'])) {
                        $queue['ext'][] = $extension;
                        break;
                    }
                }
                unset($queue); // bonne pratique avec les références
            }
        }

        if (str_starts_with($request->action_type, 'delete_group_')) {
            $index = (int) str_replace('delete_group_', '', $request->action_type);
            unset($callGroups[$index]);
            $callGroups = array_values($callGroups);
        }

        if (str_starts_with($request->action_type, 'delete_queue_')) {
            $index = (int) str_replace('delete_queue_', '', $request->action_type);
            unset($queues[$index]);
            $queues = array_values($queues);
        }

        if (str_starts_with($request->action_type, 'delete_ext|')) {
            [, $groupName, $extIndex] = explode('|', $request->action_type, 3);
            $foundInCallGroup = false;

            // Chercher dans les call groups
            foreach ($callGroups as &$group) {
                if ($group['name'] === $groupName) {
                    if (isset($group['ext'][$extIndex])) {
                        unset($group['ext'][$extIndex]);
                        $group['ext'] = array_values($group['ext']); // Réindexation propre
                    }
                    $foundInCallGroup = true;
                    break;
                }
            }
            unset($group);

            // Si pas trouvé dans les call groups, chercher dans les queues
            if (!$foundInCallGroup) {
                foreach ($queues as &$queue) {
                    if ($queue['name'] === $groupName) {
                        if (isset($queue['ext'][$extIndex])) {
                            unset($queue['ext'][$extIndex]);
                            $queue['ext'] = array_values($queue['ext']); // Réindexation propre
                        }
                        break;
                    }
                }
                unset($queue);
            }
        }

        session(['form_yeastar.callgroups' => $callGroups]);
        session(['form_yeastar.queues' => $queues]);
        return redirect()->route('yeastar.call_group');
    }

    // Timetable (H.O.)
    public function timetable()
    {
        $data = Session::get('form_yeastar', []);

        return view('yeastar.timetable', compact('data'));
    }

    public function postTimetable(Request $request)
    {
        $validated = $request->validate([
            'timetable_ho' => 'nullable|string',
        ]);

        Session::put('form_yeastar.timetable_ho', $validated['timetable_ho']);

        return redirect()->route('yeastar.svi');
    }

    // Dialplan
    public function dialplan()
    {
        $data = Session::get('form_yeastar', []);
        return view('yeastar.dialplan', compact('data'));
    }

    public function postDialplan(Request $request)
    {
        $validated = $request->validate([
            'dialplan' => 'required|string',
        ]);

        Session::put('form_yeastar.dialplan', $validated['dialplan']);

        return redirect()->route('yeastar.infos');
    }


    // SVI
    public function svi()
    {
        $data = Session::get('form_yeastar', []);

        return view('yeastar.svi', compact('data'));
    }

    public function postSvi(Request $request)
    {
        $validated = $request->validate([
            'svi' => 'nullable|string',
        ]);

        Session::put('form_yeastar.svi', $validated['svi']);

        return redirect()->route('yeastar.dialplan');
    }

    // SVI OLD
    public function svi_old()
    {
        $data = Session::get('form', []);

        return view('yeastar.svi', compact('data'));
    }

    public function postSvi_old(Request $request)
    {
        $sviActive = $request->boolean('svi_enabled');

        if ($sviActive) {
            $options = $request->input('svi', []);
            // Nettoyage et tri des options si nécessaire
            $options = collect($options)
                ->filter(fn($opt) => !empty($opt['nom'])) // ignore les vides
                ->sortBy('ordre')
                ->values()
                ->all();

            session([
                'form_yeastar.svi_enabled' => true,
                'form_yeastar.svi_options' => $options,
            ]);

            return redirect()->back()->with('success', 'Options SVI enregistrées en session.');
        }

        // SVI désactivé : on efface la session
        session()->forget(['form_yeastar.svi_enabled', 'form_yeastar.svi_options']);

        return redirect()->back()->with('info', 'SVI désactivé et supprimé.');
    }

    // Infos et remarques
    public function infos()
    {
        $data = Session::get('form', []);

        if (!session('form_yeastar.dialplan')) {
            return back()->with('error', 'Dialplan obligatoire.');
        }

        return view('yeastar.infos', compact('data'));
    }

    public function postInfos(Request $request)
    {
        $validated = $request->validate([
            'infos_remarques' => 'nullable|string',
        ]);

        Session::put('form_yeastar.infos_remarques', $validated['infos_remarques']);

        return redirect()->route('yeastar.recap');
    }

    public function recap()
    {
        $data = Session::get('form_yeastar');
        return view('yeastar.recap', compact('data'));
    }

    private function dropSession()
    {
        session()->flush();
    }

    public function export(Request $request)
    {

        // Récupère les données depuis la session
        $extensions = session('form_yeastar.extensions', []);
        $portes = session('form_yeastar.numeros.portes', []);
        $urlPbx = session('form_yeastar.url_pbx');
        $reseller_name = session('form_yeastar.reseller_name');
        $reseller_email = session('form_yeastar.reseller_email');
        $customer_name = session('form_yeastar.customer_name');
        $callGroups = session('form_yeastar.callgroups', []);
        $queues = session('form_yeastar.queues', []);
        $svi = session('form_yeastar.svi');
        $timetable_ho = session('form_yeastar.timetable_ho');
        $dialplan = session('form_yeastar.dialplan');
        $infos_remarques = session('form_yeastar.infos_remarques');
        // $devices = session('form_yeastar.devices');

        if (!empty($request->input('website'))) {
            abort(403, 'Spam détecté.');
        }

        $data = [
            'reseller_name' => $reseller_name,
            'customer_name' => $customer_name,
            'urlPbx' => $urlPbx,
            'portes' => $portes,
            'extensions' => $extensions,
            'callGroups' => $callGroups,
            'queues' => $queues,
            'timetable_ho' => $timetable_ho,
            'svi' => $svi,
            'dialplan' => $dialplan,
            'infos_remarques' => $infos_remarques,
            // 'devices' => $devices,
            'reseller_email' => $reseller_email,
        ];

        $pdf = Pdf::loadView('pdf.yeastar', $data);
        $content = $pdf->output();

        $data['pdf'] = $content;

        // $mail = 'support@kastor.biz';
        $mail = 't.vaquie@kiwi.tel';

        Mail::to($mail)->cc($reseller_email)->send(new MailerFormulaireYeastar($data));


        // $this->dropSession();

        return redirect()->route('home')->with('success', 'Mail envoyé avec pièces-jointes.');
    }
}
