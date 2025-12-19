<?php

// ************************
// *    Timothé VAQUIÉ    *
// *    Version : 3.0     *
// ************************

namespace App\Http\Controllers;

use App\Mail\MailerFormulaireWildix ;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Rules\ValidExtensionWildix;

class WildixFormController extends Controller
{
    // IPBX
    public function generalInfo()
    {
        $data = Session::get('form_wildix', []);
        return view('wildix.general_info', compact('data'));
    }

    public function postGeneralInfo(Request $request)
    {
        // Sauvegarder les données même si on revient en arrière
        if ($request->has('reseller_name')) {
            Session::put('form_wildix.reseller_name', $request->input('reseller_name'));
        }
        if ($request->has('reseller_email')) {
            Session::put('form_wildix.reseller_email', $request->input('reseller_email'));
        }
        if ($request->has('customer_name')) {
            Session::put('form_wildix.customer_name', $request->input('customer_name'));
        }
        if ($request->has('url_pbx')) {
            Session::put('form_wildix.url_pbx', str_replace(' ', '', strtolower($request->input('url_pbx'))));
        }

        // Si on clique sur Précédent, rediriger vers la page précédente
        if ($request->has('previous')) {
            return redirect()->route('home');
        }

        $validated = $request->validate([
            'reseller_name' => 'required|string',
            'reseller_email' => 'required|string',
            'customer_name' => 'required|string',
            'url_pbx' => 'required|string',
        ]);

        Session::put('form_wildix.reseller_name', $validated['reseller_name']);
        Session::put('form_wildix.reseller_email', $validated['reseller_email']);
        Session::put('form_wildix.customer_name', $validated['customer_name']);
        Session::put('form_wildix.url_pbx', str_replace(' ', '', strtolower($validated['url_pbx'])));

        return redirect()->route('wildix.num_list');
    }

    // numList
    public function numList()
    {
        $dataForm = Session::get('form_wildix', []);
        $data['portes'] = session('form_wildix.numeros.portes', []);

        if (!session('form_wildix.url_pbx')) {
            return redirect()->route('wildix.general_info')->with('error', 'Des informations sont manquantes pour continuer.');
        }
        return view('wildix.num-list', compact('data', 'dataForm'));
    }

    public function postNumList(Request $request)
    {
        $action = $request->input('action_type');
        $portes = session()->get('form_wildix.numeros.portes', []);

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

            $portes = session()->get('form_wildix.numeros.portes', []);

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

            session()->put('form_wildix.numeros.portes', $portes);
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

            $portes = session()->get('form_wildix.numeros.portes', []);

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

            session()->put('form_wildix.numeros.portes', $portes);
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

        session()->put('form_wildix.numeros.portes', $portes);
        return back()->withInput();
    }

    // Extensions

    public function extension()
    {
        $data = Session::get('form', []);

        $portes = session('form_wildix.numeros.portes', []);

        if (empty($portes)) {
            return redirect()
                ->route('wildix.num_list')
                ->with([
                    'error' => 'Vous devez au minimum renseigner un numéro porté/créé.',
                ]);
        }

        return view('wildix.extension', ['data' => ['portes' => $portes]]);
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
            session()->put('form_wildix.extensions', $extensions);

            return redirect()->back()->with('success', 'Extension supprimée.');
        }

        $validated = $request->validate([
            'extensions' => 'required|array|min:1',
            'extensions.*.extension' => ['required', new ValidExtensionWildix($extensions)],
            'extensions.*.name' => 'required|string',
            'extensions.*.email' => 'nullable|email',
            'extensions.*.numPorte' => 'required|string',
            'extensions.*.language' => 'required|string|in:fr,en,it,es',
            'extensions.*.licence' => 'required|string|in:service,basic,essential,business,premium',
        ]);

        // Si on clique sur Précédent, sauvegarder sans validation stricte et rediriger
        if ($request->has('previous')) {
            // Sauvegarder ce qui est valide, même si incomplet
            if (!empty($extensions)) {
                session()->put('form_wildix.extensions', $extensions);
            }
            return redirect()->route('wildix.num_list');
        }

        session()->put('form_wildix.extensions', $validated['extensions']);

        return redirect()->route('wildix.device')->with('success', 'Extensions sauvegardées !');
    }

    // Devices
    public function devices()
    {
        $data = Session::get('form_wildix');

        if (!session('form_wildix.extensions')) {
            return back()->with('error', 'Au moins une extension est obligatoire pour continuer.');
        }

        return view('wildix.devices', compact('data'));
    }

    public function postDevices(Request $request)
    {
        // Si on clique sur Précédent, rediriger vers la page précédente
        if ($request->has('previous')) {
            return redirect()->route('wildix.extension');
        }

        $devices = session('form_wildix.devices');

        if ($request->action_type === 'add_device') {
            $validated = $request->validate([
                'device_name' => 'required|string',
                'extension' => 'nullable|string',
            ]);

            $form = session('form_wildix', []);

            $form['devices'][] = [
                'device_name' => $validated['device_name'],
                'extension' => $validated['extension'] ?? null,
            ];

            if (isset($validated['extension'])) {
                $extensionData = collect($form['extensions'])->firstWhere('extension', $validated['extension']);

                if ($extensionData) {
                    $licence = strtolower($extensionData['licence']);

                    $userName = $extensionData['name'] ?? null;

                    $userExtensions = collect($form['extensions'])->filter(fn($ext) => $ext['name'] === $userName)->pluck('extension')->toArray();

                    if ($licence === 'basic') {
                        $deviceCount = collect($form['devices'])->filter(fn($device) => $device['extension'] && in_array($device['extension'], $userExtensions))->count();

                        if ($deviceCount > 1) {
                            return redirect()
                                ->back()
                                ->with('error', "L'extension \"" . $validated['extension'] . ' - ' . $userName . "\" ayant une licence BASIC ne peut avoir qu’un seul équipement.");
                        }
                    } elseif ($licence === 'service') {
                        return redirect()
                            ->back()
                            ->with('error', "L'extension \"" . $validated['extension'] . ' - ' . $userName . "\" ayant une licence SERVICE ne peut pas être lié à un équipement.");
                    }
                }
            }
            session(['form_wildix' => $form]);

            return redirect()->back()->with('success', 'Équipement ajouté.');
        }

        if (str_starts_with($request->action_type, 'delete_device_')) {
            $index = (int) str_replace('delete_device_', '', $request->action_type);
            unset($devices[$index]);
            $devices = array_values($devices);

            session(['form_wildix.devices' => $devices]);

            return redirect()->back()->with('success', 'Équipement retiré.');
        }

        // $deviceNames = ['W-AIR SYNC PLUS BASE', 'W-AIR SYNC PLUS BASE OUTDOOR', 'W-AIR SMALL BUSINESS'];
        // $containsSpecialDevice = collect($devices)->contains(function ($device) use ($deviceNames) {
        //     return in_array($device['device_name'], $deviceNames);
        // });

        // if ($containsSpecialDevice) {
        //     return redirect()->route('form.dect');
        // } else {
            return redirect()->route('wildix.call_group');
        // }
    }

    // Devices Phones
    public function dect()
    {
        $data = Session::get('form');

        if (!session('form_wildix.extensions')) {
            return back()->with('error', 'Au moins une extension est obligatoire pour continuer.');
        }

        return view('wildix.dect', compact('data'));
    }

    public function postDect(Request $request) {}

    // CALLGROUPS
    public function callGroup()
    {
        if (!session('form_wildix.extensions')) {
            return redirect()->route('wildix.extension')->with('error', 'Au moins une extension est obligatoire pour continuer.');
        }

        $extensions = session('form_wildix.extensions', []);
        $callGroups = session('form_wildix.callgroups', []);
        $queues = session('form_wildix.queues', []);

        // if (!session('form_wildix.devices')) {
        //     return back()->with('error', 'Au moins un équipement pour continuer.');
        // }
        
        return view('wildix.call_group', compact('extensions', 'callGroups', 'queues'));
    }

    public function postCallGroup(Request $request)
    {
        // Si on clique sur Précédent, rediriger vers la page précédente
        if ($request->has('previous')) {
            return redirect()->route('wildix.device');
        }

        $callGroups = session('form_wildix.callgroups', []);
        $queues = session('form_wildix.queues', []);

        if ($request->action_type === 'add_group') {
            $request->validate([
                'cgName' => 'required|string',
                'cg_type' => 'required|string',
            ]);

            // Récupérer les extensions sélectionnées lors de la création
            $extensionsNew = $request->input('ext_selectionne_new', []);
            $extensionsList = [];

            // Nettoyer et valider les extensions
            if (!empty($extensionsNew) && is_array($extensionsNew)) {
                foreach ($extensionsNew as $extension) {
                    if ($extension && !empty(trim($extension))) {
                        $extensionsList[] = trim($extension);
                    }
                }
            }

            $callGroups[] = [
                'name' => $request->cgName,
                'type' => $request->cg_type,
                'ext' => $extensionsList,
            ];

            // Message de succès avec le nombre d'extensions ajoutées
            $message = 'Groupe d\'appel créé avec succès.';
            if (count($extensionsList) > 0) {
                $message .= ' ' . count($extensionsList) . ' extension(s) ajoutée(s).';
            }
            session()->flash('success', $message);
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
            $extensions = $request->input('ext_selectionne', []);
            $foundInCallGroup = false;
            $foundInQueue = false;

            // Vérifier que le groupe est sélectionné
            if (empty($groupName)) {
                return redirect()->route('wildix.call_group')
                    ->with('error', 'Veuillez sélectionner un groupe d\'appel.');
            }

            // Vérifier que des extensions ont été sélectionnées
            if (empty($extensions) || !is_array($extensions)) {
                return redirect()->route('wildix.call_group')
                    ->with('error', 'Veuillez sélectionner au moins une extension.');
            }

            // Chercher dans les call groups
            foreach ($callGroups as &$group) {
                if ($group['name'] === $groupName) {
                    $addedCount = 0;
                    foreach ($extensions as $extension) {
                        if ($extension && !in_array($extension, $group['ext'])) {
                            $group['ext'][] = $extension;
                            $addedCount++;
                        }
                    }
                    $foundInCallGroup = true;
                    if ($addedCount > 0) {
                        session()->flash('success', $addedCount . ' extension(s) ajoutée(s) au groupe avec succès.');
                    }
                    break;
                }
            }
            unset($group); // bonne pratique avec les références

            // Si pas trouvé dans les call groups, chercher dans les queues
            if (!$foundInCallGroup) {
                foreach ($queues as &$queue) {
                    if ($queue['name'] === $groupName) {
                        $addedCount = 0;
                        foreach ($extensions as $extension) {
                            if ($extension && !in_array($extension, $queue['ext'])) {
                                $queue['ext'][] = $extension;
                                $addedCount++;
                            }
                        }
                        $foundInQueue = true;
                        if ($addedCount > 0) {
                            session()->flash('success', $addedCount . ' extension(s) ajoutée(s) à la file d\'attente avec succès.');
                        }
                        break;
                    }
                }
                unset($queue); // bonne pratique avec les références
            }

            // Si le groupe n'a pas été trouvé
            if (!$foundInCallGroup && !$foundInQueue) {
                return redirect()->route('wildix.call_group')
                    ->with('error', 'Le groupe sélectionné n\'existe pas.');
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

        session(['form_wildix.callgroups' => $callGroups]);
        session(['form_wildix.queues' => $queues]);
        return redirect()->route('wildix.call_group');
    }

    // Timetable (H.O.)
    public function timetable()
    {
        $data = Session::get('form_wildix', []);

        if (!session('form_wildix.extensions')) {
            return redirect()->route('wildix.extension')->with('error', 'Au moins une extension est obligatoire pour continuer.');
        }

        return view('wildix.timetable', compact('data'));
    }

    public function postTimetable(Request $request)
    {
        // Sauvegarder les données même si on revient en arrière
        if ($request->has('timetable_ho')) {
            Session::put('form_wildix.timetable_ho', $request->input('timetable_ho'));
        }

        // Si on clique sur Précédent, rediriger vers la page précédente
        if ($request->has('previous')) {
            return redirect()->route('wildix.call_group');
        }

        $validated = $request->validate([
            'timetable_ho' => 'nullable|string',
        ]);

        Session::put('form_wildix.timetable_ho', $validated['timetable_ho']);

        return redirect()->route('wildix.svi');
    }

    // Dialplan
    public function dialplan()
    {
        if (!session('form_wildix.extensions')) {
            return redirect()->route('wildix.extension')->with('error', 'Au moins une extension est obligatoire pour continuer.');
        }

        $data = Session::get('form_wildix', []);
        return view('wildix.dialplan', compact('data'));
    }

    public function postDialplan(Request $request)
    {
        // Sauvegarder les données même si on revient en arrière
        if ($request->has('dialplan')) {
            Session::put('form_wildix.dialplan', $request->input('dialplan'));
        }

        // Si on clique sur Précédent, rediriger vers la page précédente
        if ($request->has('previous')) {
            return redirect()->route('wildix.svi');
        }

        $validated = $request->validate([
            'dialplan' => 'required|string',
        ]);

        Session::put('form_wildix.dialplan', $validated['dialplan']);

        return redirect()->route('wildix.infos');
    }


    // SVI
    public function svi()
    {
        $data = Session::get('form_wildix', []);

        if (!session('form_wildix.extensions')) {
            return redirect()->route('wildix.extension')->with('error', 'Au moins une extension est obligatoire pour continuer.');
        }

        return view('wildix.svi', compact('data'));
    }

    public function postSvi(Request $request)
    {
        // Sauvegarder les données même si on revient en arrière
        if ($request->has('svi')) {
            Session::put('form_wildix.svi', $request->input('svi'));
        }

        // Si on clique sur Précédent, rediriger vers la page précédente
        if ($request->has('previous')) {
            return redirect()->route('wildix.timetable');
        }

        $validated = $request->validate([
            'svi' => 'nullable|string',
        ]);

        Session::put('form_wildix.svi', $validated['svi']);

        return redirect()->route('wildix.dialplan');
    }

    // SVI OLD
    public function svi_old()
    {
        $data = Session::get('form', []);

        return view('wildix.svi', compact('data'));
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
                'form_wildix.svi_enabled' => true,
                'form_wildix.svi_options' => $options,
            ]);

            return redirect()->back()->with('success', 'Options SVI enregistrées en session.');
        }

        // SVI désactivé : on efface la session
        session()->forget(['form_wildix.svi_enabled', 'form_wildix.svi_options']);

        return redirect()->back()->with('info', 'SVI désactivé et supprimé.');
    }

    // Infos et remarques
    public function infos()
    {
        $data = Session::get('form_wildix', []);

        if (!session('form_wildix.dialplan')) {
            return back()->with('error', 'Dialplan obligatoire.');
        }

        return view('wildix.infos', compact('data'));
    }

    public function postInfos(Request $request)
    {
        // Sauvegarder les données même si on revient en arrière
        if ($request->has('infos_remarques')) {
            Session::put('form_wildix.infos_remarques', $request->input('infos_remarques'));
        }

        // Si on clique sur Précédent, rediriger vers la page précédente
        if ($request->has('previous')) {
            return redirect()->route('wildix.dialplan');
        }

        $validated = $request->validate([
            'infos_remarques' => 'nullable|string',
        ]);

        Session::put('form_wildix.infos_remarques', $validated['infos_remarques']);

        return redirect()->route('wildix.recap');
    }

    public function recap()
    {
        $data = Session::get('form_wildix');

        if (!session('form_wildix.dialplan')) {
            return redirect()->route('wildix.dialplan')->with('error', 'Dialplan obligatoire.');
        }
        
        return view('wildix.recap', compact('data'));
    }

    private function dropSession()
    {
        Session::forget('form_wildix');
    }

    public function export(Request $request)
    {

        // Récupère les données depuis la session
        $extensions = session('form_wildix.extensions', []);
        $portes = session('form_wildix.numeros.portes', []);
        $urlPbx = session('form_wildix.url_pbx');
        $reseller_name = session('form_wildix.reseller_name');
        $reseller_email = session('form_wildix.reseller_email');
        $customer_name = session('form_wildix.customer_name');
        $callGroups = session('form_wildix.callgroups', []);
        $queues = session('form_wildix.queues', []);
        $svi = session('form_wildix.svi');
        $timetable_ho = session('form_wildix.timetable_ho');
        $dialplan = session('form_wildix.dialplan');
        $infos_remarques = session('form_wildix.infos_remarques');
        $devices = session('form_wildix.devices');

        if (!empty($request->input('website'))) {
            abort(403, 'Spam détecté.');
        }

        // Création du fichier Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Id', 'SourceId', 'Type', 'Name', 'Login', 'Phone', 'Fax', 'OfficePhone', 'Email', 'Mobile', 'Dialplan', 'FaxDialplan', 'Lang', 'Group', 'Department', 'ImageUrl', 'Password', 'SipPassword', 'LicenseType', 'PBX'];
        $sheet->fromArray($headers, null, 'A1');

        $row = 2;

        $usedIds = [];

        foreach ($extensions as $ext) {
            $prefix = substr($ext['extension'], 0, 3);
            $randomLength = 7 - strlen($prefix);

            // Boucle jusqu'à obtenir un ID unique
            do {
                $randomPart = str_pad(rand(0, pow(10, $randomLength) - 1), $randomLength, '0', STR_PAD_LEFT);
                $result = $prefix . $randomPart;
            } while (in_array($result, $usedIds));

            $usedIds[] = $result; // Marque l'ID comme utilisé

            if ($ext['licence'] == 'service') {
                $ext['licence'] = 'pbxService';
            }

            $data = [$result, $ext['extension'] ?? '', 'user', $ext['name'] ?? '', '', $ext['extension'] ?? '', '', '="' . $ext['numPorte'] ?? '', $ext['email'] ?? '', '', 'users', 'users', $ext['language'] ?? '', 'Default', '', '', '', '', $ext['licence'] ?? '', ''];

            $col = 'A';
            foreach ($data as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }

        $filename = $urlPbx . '_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $directory = storage_path('app/temp');
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $path = storage_path("app/temp/$filename");
        $writer = new Csv($spreadsheet);

        $writer->setDelimiter(',');
        $writer->setEnclosure('"');
        $writer->setLineEnding("\r\n"); // Pour compatibilité Windows
        $writer->setSheetIndex(0); // Assure que c’est bien la première feuille

        $writer->save($path);

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
            'devices' => $devices,
            'reseller_email' => $reseller_email,
            'fichier' => $path,
        ];

        $pdf = Pdf::loadView('pdf.wildix', $data);
        $content = $pdf->output();

        $data['pdf'] = $content;

        $mail = config('mail.mail_to');

        Mail::to($mail)->cc($reseller_email)->send(new MailerFormulaireWildix($data));

        unlink($path);

        $this->dropSession();

        return redirect()->route('home')->with('success', 'Mail envoyé avec pièces-jointes.');
    }
}
