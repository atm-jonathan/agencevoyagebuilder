<?php
/* Copyright (C) 2023 SuperAdmin
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
 * \file    formulevoyage/lib/formulevoyage.lib.php
 * \ingroup formulevoyage
 * \brief   Library files with common functions for Formulevoyage
 */

/**
 * Prepare admin pages header
 *
 * @return array
 */
function formulevoyageAdminPrepareHead()
{
	global $langs, $conf;

	// global $db;
	// $extrafields = new ExtraFields($db);
	// $extrafields->fetch_name_optionals_label('formule');

	$langs->load("formulevoyage@formulevoyage");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/formulevoyage/admin/setup.php", 1);
	$head[$h][1] = $langs->trans("Settings");
	$head[$h][2] = 'settings';
	$h++;

	/*
	$head[$h][0] = dol_buildpath("/formulevoyage/admin/formule_extrafields.php", 1);
	$head[$h][1] = $langs->trans("ExtraFields");
	$nbExtrafields = is_countable($extrafields->attributes['formule']['label']) ? count($extrafields->attributes['formule']['label']) : 0;
	if ($nbExtrafields > 0) {
		$head[$h][1] .= ' <span class="badge">' . $nbExtrafields . '</span>';
	}
	$head[$h][2] = 'formule_extrafields';
	$h++;
	*/
    $head[$h][0] = dol_buildpath("/formulevoyage/admin/tarifpays.php", 1);
    $head[$h][1] = $langs->trans("tarifpays");
    $head[$h][2] = 'tarifpays';
    $h++;

    $head[$h][0] = dol_buildpath("/formulevoyage/admin/about.php", 1);
    $head[$h][1] = $langs->trans("About");
    $head[$h][2] = 'about';
    $h++;


    // Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	//$this->tabs = array(
	//	'entity:+tabname:Title:@formulevoyage:/formulevoyage/mypage.php?id=__ID__'
	//); // to add new tab
	//$this->tabs = array(
	//	'entity:-tabname:Title:@formulevoyage:/formulevoyage/mypage.php?id=__ID__'
	//); // to remove a tab
	complete_head_from_modules($conf, $langs, null, $head, $h, 'formulevoyage@formulevoyage');

	complete_head_from_modules($conf, $langs, null, $head, $h, 'formulevoyage@formulevoyage', 'remove');

	return $head;
}
/**
 * Si tarif existant return le tarif
 * @param string|int $id_country
 * @return string   tarif
 */
function checkTarifPays($id_country) {
    global $db;
    $tReq = array();
    $sql = "SELECT tarif FROM ";
    $sql .= MAIN_DB_PREFIX . "country_tarif ";
    $sql .= "WHERE fk_country = " . $id_country;
    $resql = $db->query($sql);
    while ($tArr = $db->fetch_array($resql)){
        $tReq[] = $tArr;
    }
    return  $tReq[0][0];
}

function modififyContentIputTarif($country) {
    global $conf;
    if (!empty($country) && $country != -1 && $country != 0){
        $tarifPaysSelect = checkTarifPays($country);
        if (empty($tarifPaysSelect)){
            $_POST['tarif'] = $conf->global->tarifdefaut;
            setEventMessage('noTarifCountry');
        }else{
            $_POST['tarif'] = $tarifPaysSelect;
        }
    }else{
        $_POST['tarif'] = $conf->global->tarifdefaut;
        setEventMessage('noCountrySelect');
    }
}

//    return $result;

/**
 * Met à jour le tarif du pays
 * @param string|int $id_country
 * @param  string|int $tarif
 * @return int
 */
function updateTarifCountry($id_country, $tarif) {
    global $db;
    $sql = "UPDATE " . MAIN_DB_PREFIX . "country_tarif SET tarif =" . $tarif;
    $sql .= " WHERE " . MAIN_DB_PREFIX . "country_tarif.fk_country =" . $id_country;
    $resql = $db->query($sql);
    return $resql;
}

/**
 * Insert le tarif du pays
 * @param string|int $id_country
 * @param string|int $tarif
 * @return array
 */
function insertTarifCountry($id_country, $tarif) {
    global $db;
    $sql = "INSERT INTO ".MAIN_DB_PREFIX ;
    $sql .= "country_tarif (`fk_country`, `tarif`)";
    $sql .= "VALUES ($id_country, $tarif)";
    $resql = $db->query($sql);

    return $resql;
}

