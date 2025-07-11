<?php

namespace App\Http\Controllers;

use App\Mail\MailerFormulaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Illuminate\Support\Facades\File;

class MultiStepFormController extends Controller
{
    // IPBX
    public function pbxInfo()
    {
        $data = Session::get('form', []);
        return view('form.url_pbx', compact('data'));
    }

    public function postPbxInfo(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string',
            'url_pbx' => 'required|string',
        ]);

        Session::put('form.customer_name', $validated['customer_name']);
        Session::put('form.url_pbx', str_replace(' ', '', $validated['url_pbx']));

        return redirect()->route('form.num-list');
    }

    // numList
    public function numList()
    {
        $dataForm = Session::get('form', []);
        $data['portes'] = session('form.numeros.portes', []);
        return view('form.num_list', compact('data', 'dataForm'));
    }

    public function postNumList(Request $request)
    {
        $action = $request->input('action_type');
        $portes = session()->get('form.numeros.portes', []);

        // Ajouter un numéro porté
        if ($action === 'ajouter_porte') {
            $validated = $request->validate(
                [
                    'numero_porte' => ['required', 'string', 'regex:/^\+33(1|2|3|4|5|8|9)\d{8}$/'],
                ],
                [
                    'numero_porte.required' => 'Le numéro porté est obligatoire.',
                    'numero_porte.regex' => 'Le numéro porté doit être au format +33 suivi de 9 chiffres.',
                ],
            );

            $portes = session()->get('form.numeros.portes', []);

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

            session()->put('form.numeros.portes', $portes);
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
                    'numero_provisoire.regex' => 'Le numéro provisoire doit être au format +33 suivi de 9 chiffres.',
                    'porte_selectionne.required' => 'Vous devez sélectionner un numéro porté existant.',
                ],
            );

            $portes = session()->get('form.numeros.portes', []);
            $portes_exists = collect($portes)->pluck('numero')->contains($validated['numero_provisoire']);

            foreach ($portes as &$porte) {
                if ($portes_exists) {
                    return back()
                        ->withErrors(['numero_provisoire' => 'Ce numéro est déjà inscrit comme numéro portés/créés.'])
                        ->withInput();
                } else {
                    $porte['provisoire'] = $validated['numero_provisoire'];
                    break;
                }
            }
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

        session()->put('form.numeros.portes', $portes);
        return back()->withInput();
    }

    // Extensions

    public function extension()
    {
        $data = Session::get('form', []);

        $portes = session('form.numeros.portes', []);

        if (empty($portes)) {
            return redirect()
                ->route('form.num-list')
                ->withErrors([
                    'access_denied' => 'Refusé : Vous devez d’abord ajouter au moins un numéro porté.',
                ]);
        }

        // return view('form.extension', compact('data', 'portes'));
        return view('form.extension', ['data' => ['portes' => $portes]]);
    }

    public function postExtension(Request $request)
    {
        // Récupère les extensions déjà en session
        $extensions = session('form.extensions', []);

        // Si c'est un bouton "delete" qui a été cliqué
        if ($request->has('delete')) {
            $indexToDelete = $request->input('delete');

            // Supprime l'élément de la liste
            unset($extensions[$indexToDelete]);

            // Réindexe le tableau
            $extensions = array_values($extensions);

            // Sauvegarde en session
            session()->put('form.extensions', $extensions);

            return redirect()->back()->with('success', 'Extension supprimée.');
        }

        // Sinon, on traite l'ajout / mise à jour normale
        $validated = $request->validate([
            'extensions' => 'required|array',
            'extensions.*.extension' => 'required|numeric',
            'extensions.*.name' => 'required|string',
            'extensions.*.email' => 'nullable|string',
            'extensions.*.numPorte' => 'required|string',
            'extensions.*.language' => 'required|string',
            'extensions.*.licence' => 'required|string',
        ]);

        // Vérification des doublons
        $extensionNums = array_column($validated['extensions'], 'extension');
        if (count($extensionNums) !== count(array_unique($extensionNums))) {
            return back()->withErrors(['extensions.unique' => 'Deux extensions ou plus ont le même numéro.']);
        }

        session()->put('form.extensions', $validated['extensions']);

        return redirect()->back()->with('success', 'Extensions sauvegardées.');
    }

    // CALLGROUPS
    public function callGroup()
    {
        $extensions = session('form.extensions', []);
        $callGroups = session('form.callgroups', []);

        return view('form.call_group', compact('extensions', 'callGroups'));
    }

    public function postCallGroup(Request $request)
    {
        $callGroups = session('form.callgroups', []);

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

        if ($request->action_type === 'add_ext') {
            $groupName = $request->input('cg_selectionne');
            $extension = $request->input('ext_selectionne');

            foreach ($callGroups as &$group) {
                if ($group['name'] === $groupName && $extension && !in_array($extension, $group['ext'])) {
                    $group['ext'][] = $extension;
                    break;
                }
            }
            unset($group); // bonne pratique avec les références
        }

        if (str_starts_with($request->action_type, 'delete_group_')) {
            $index = (int) str_replace('delete_group_', '', $request->action_type);
            unset($callGroups[$index]);
            $callGroups = array_values($callGroups);
        }

        if (str_starts_with($request->action_type, 'delete_ext|')) {
            [, $groupName, $extIndex] = explode('|', $request->action_type, 3);

            foreach ($callGroups as &$group) {
                if ($group['name'] === $groupName) {
                    if (isset($group['ext'][$extIndex])) {
                        unset($group['ext'][$extIndex]);
                        $group['ext'] = array_values($group['ext']); // Réindexation propre
                    }
                    break;
                }
            }
            unset($group);
        }

        session(['form.callgroups' => $callGroups]);
        return redirect()->route('form.call-group');
    }

    // Timetable (H.O.)
    public function timetable()
    {
        $data = Session::get('form', []);
        return view('form.timetable', compact('data'));
    }

    public function postTimetable(Request $request)
    {
        $validated = $request->validate([
            'timetable_ho' => 'required|string',
        ]);

        Session::put('form.timetable_ho', $validated['timetable_ho']);

        return redirect()->route('form.dialplan');
    }

    // Dialplan
    public function dialplan()
    {
        $data = Session::get('form', []);
        return view('form.dialplan', compact('data'));
    }

    public function postDialplan(Request $request)
    {
        $validated = $request->validate([
            'dialplan' => 'required|string',
        ]);

        Session::put('form.dialplan', $validated['dialplan']);

        return redirect()->route('form.svi');
    }

    // SVI
    public function svi()
    {
        $data = Session::get('form', []);
        return view('form.svi', compact('data'));
    }

    public function postSvi(Request $request)
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
                'form.svi_enabled' => true,
                'form.svi_options' => $options,
            ]);

            return redirect()->back()->with('success', 'Options SVI enregistrées en session.');
        }

        // SVI désactivé : on efface la session
        session()->forget(['form.svi_enabled', 'form.svi_options']);

        return redirect()->back()->with('info', 'SVI désactivé et supprimé.');
    }

    // Infos et remarques
    public function infos()
    {
        $data = Session::get('form', []);
        return view('form.infos', compact('data'));
    }

    public function postInfos(Request $request)
    {
        $validated = $request->validate([
            'infos_remarques' => 'nullable|string',
        ]);

        Session::put('form.infos_remarques', $validated['infos_remarques']);

        return redirect()->route('form.recap');
    }

    // OLD
    public function recap()
    {
        $data = Session::get('form');
        return view('form.recap', compact('data'));
    }

    public function postRecap()
    {
        $data = Session::get('form');

        // Enregistrement en BDD par exemple
        \App\Models\UserForm::create($data);

        // Nettoyage de session
        Session::forget('form');

        return redirect()->route('home')->with('success', 'Formulaire soumis avec succès !');
    }

    public function sessionDrop(Request $request)
    {
        try {
            $request->session()->pull('extensions', 'default');
            $request->session()->pull('portes', 'default');
            $request->session()->pull('url_pbx', 'default');
            $request->session()->pull('call_groups', 'default');
            $request->session()->pull('svi_options', 'default');
            $request->session()->pull('timetable', 'default');

            return redirect()->back()->with('success', 'Vidage de la session réussite.');
        } catch (Throwable $th) {
            return redirect()->back()->with('error', 'Erreur lors du vidage de la session.');
        }
    }

    public function export(Request $request)
    {
        // Récupère les données depuis la session
        $extensions = session('form.extensions', []);
        $portes = session('form.numeros.portes', []);
        $urlPbx = session('form.url_pbx', '');
        $customer_name = session('form.customer_name', '');
        $callGroups = session('form.callgroups', []);
        $svi_options = session('form.svi_options');
        $timetable = session('form.timetable_ho');
        $dialplan = session('form.dialplan');
        $infos_remarques = session('form.infos_remarques');

        if (!empty($request->input('website'))) {
            // Optionnel : logger ou bloquer l'IP
            abort(403, 'Spam détecté.');
        }
        // dd($extensions, $portes, $customer_name, $urlPbx, $callGroups, $svi_options, $timetable, $dialplan, $infos_remarques);

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

            // $sheet->setCellValue('A' . $row, $result . ',"' . $ext['extension'] . '","user","' . $ext['nom'] . '",,"' . $ext['extension'] . '",,"' . $ext['numPorte'] . '",,,"users","users","' . $ext['language'] . '","Default",,,,,"' . ($ext['licence'] ?? '') . '",');
            // $row++;
            $data = [$result, $ext['extension'] ?? '', 'user', $ext['name'] ?? '', '', $ext['extension'] ?? '', '', $ext['numPorte'] ?? '', $ext['email'] ?? '', '', 'users', 'users', $ext['language'] ?? '', 'Default', '', '', '', '', $ext['licence'] ?? '', ''];

            $col = 'A';
            foreach ($data as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
        }

        $filename = $urlPbx . '_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $directory = storage_path('app/temp');
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $path = storage_path("app/temp/$filename");
        // $writer = new Xlsx($spreadsheet);
        $writer = new Csv($spreadsheet);

        // Optionnel : configuration du séparateur (virgule par défaut, mais tu peux le changer)
        $writer->setDelimiter(',');
        // Tu peux aussi définir l’enclosure si nécessaire
        $writer->setEnclosure('"');
        $writer->setLineEnding("\r\n"); // Pour compatibilité Windows
        $writer->setSheetIndex(0); // Assure que c’est bien la première feuille

        $writer->save($path);

        Mail::to('timothe.vaquie1@gmail.com')->send(
            new MailerFormulaire([
                'customer_name' => $customer_name,
                'urlPbx' => $urlPbx,
                'portes' => $portes,
                'extensions' => $extensions,
                'callGroups' => $callGroups,
                'timetable' => $timetable,
                'svi_options' => $svi_options,
                'dialplan' => $dialplan,
                'infos_remarques' => $infos_remarques,
            ]),
        );

        unlink($path);

        // $this->sessionDrop($request);

        return redirect()->route('home')->with('success', 'Mail envoyé avec pièce jointe.');
    }
}
