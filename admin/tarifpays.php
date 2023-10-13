<?php
/* Copyright (C) 2004-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2023 SuperAdmin
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    formulevoyage/admin/about.php
 * \ingroup formulevoyage
 * \brief   About page of module Formulevoyage.
 */

// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
	$res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
	$i--; $j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) {
	$res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
}
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php")) {
	$res = @include dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php";
}
// Try main.inc.php using relative path
if (!$res && file_exists("../../main.inc.php")) {
	$res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
	$res = @include "../../../main.inc.php";
}
if (!$res) {
	die("Include of main fails");
}

// Libraries
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once '../lib/formulevoyage.lib.php';

global $langs;

// Translations
$langs->loadLangs(array("errors", "admin", "formulevoyage@formulevoyage"));

// Access control
if (!$user->admin) {
	accessforbidden();
}

// Parameters
$action = GETPOST('action', 'aZ09');
$backtopage = GETPOST('backtopage', 'alpha');



/*
 * Actions
 */

if ($action == "viewTarif"){
    $country_id = GETPOST('country_id', 'aZ09');
    $tarif = GETPOST('tarif', 'aZ09');
    $checkExitTarif = checkTarifPays($country_id);
    if (!empty($checkExitTarif)){
//        updateTarifCountry($country_id, $tarif);
        setEventMessage('tarifExist', 'warning');
    }else{
        insertTarifCountry($country_id, $tarif);
        setEventMessage('tarifCountryInsert');
    }
}

if ($action == 'delete' && !empty(GETPOST('id', 'int'))){
    deleteTarifCountry(GETPOST('id', 'int'));
}


/*
 * View
 */

$form = new Form($db);

$help_url = '';
$page_name = "tarifpays";
$url = $_SERVER['PHP_SELF'];

llxHeader('', $langs->trans($page_name), $help_url);

// Subheader
$linkback = '<a href="'.($backtopage ? $backtopage : DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1').'">'.$langs->trans("BackToModuleList").'</a>';

print load_fiche_titre($langs->trans($page_name), $linkback, 'title_setup');

// Configuration header
$head = formulevoyageAdminPrepareHead();
print dol_get_fiche_head($head, 'tarifpays', $langs->trans($page_name), 0, 'formulevoyage@formulevoyage');

$formAddtarif = '';
$formAddtarif = '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
$formAddtarif .= '<input type="hidden" name="token" value="'.newToken().'">';
$formAddtarif .= '<input type="hidden" name="action" value="viewTarif">';
$formAddtarif .= '<table>';
$formAddtarif .= '<tbody>';
$formAddtarif .= '<th>';
$formAddtarif .= '<div>Pays : ';
$formAddtarif .= $form->select_country('selectcountry_id');
$formAddtarif .= '</div>';
$formAddtarif .= '</th>';
$formAddtarif .= '<th>';
$formAddtarif .= '<div>Tarif :';
$formAddtarif .= '<input type="number" name="tarif">';
$formAddtarif .= '</div>';
$formAddtarif .= '</th>';
$formAddtarif .= '<th>';
$formAddtarif .= '<div>';
$formAddtarif .= '<input class="butAction" type="submit" value="'.$langs->trans('submitTarif').'">';
$formAddtarif .= '</div>';
$formAddtarif .= '</th>';
$formAddtarif .= '</tbody>';
$formAddtarif .= '</table>';
$formAddtarif .= '<div>';

$sql = "SELECT ct.rowid, c.label, ct.tarif FROM ".MAIN_DB_PREFIX."c_country c INNER JOIN llx_country_tarif ct";
$sql .= " WHERE c.rowid = ct.fk_country";
$result = $db->query($sql);
if ($result) {
    $num = $db->num_rows($result);
    $i = 0;

    $formAddtarif .=  '<div class="div-table-responsive">'; // You can use div-table-responsive-no-min if you dont need reserved height for your table
    $formAddtarif .=  '<table class="noborder centpercent">';
    $formAddtarif .=  '<tr class="liste_titre">';
    print $formAddtarif;
   print_liste_field_titre("label", $_SERVER["PHP_SELF"], "c.label", "", "", "");
   print_liste_field_titre("tarif", $_SERVER["PHP_SELF"], "ct.tarif", "", "", "");
    $rowCountry = '';
    $rowCountry =  "</tr>\n";

    if ($num > 0) {
        while ($i < $num) {
            $obj = $db->fetch_object($result);
            $rowCountry .=  '<tr class="oddeven">';
            $rowCountry .=  '<td>'.dol_escape_htmltag($obj->label).'</td>';
            $rowCountry .=  '<td>'.price(dol_escape_htmltag($obj->tarif)).' €</td>';
            $rowCountry .=  '<td><a class="reposition marginleftonly paddingleft marginrightonly paddingright" href="'.$url.'?id='.$obj->rowid.'&action=delete&token='.newToken().'">'.img_delete().'</a></td>';
            $rowCountry .=  "</tr>\n";
            $i++;
        }
    } else {
        $rowCountry .=  '<tr class="oddeven"><td colspan="7"><span class="opacitymedium">'.$langs->trans("None").'</span></td></tr>';
    }

    $rowCountry .=  '</table>';
    $rowCountry .=  '</div>';
} else {
    dol_print_error($db);
}
print $rowCountry;


// Page end
print dol_get_fiche_end();
llxFooter();
$db->close();
