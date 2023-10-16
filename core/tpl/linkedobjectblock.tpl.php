<?php
/* Copyright (C) 2010-2011  Regis Houssin <regis.houssin@inodbox.com>
 * Copyright (C) 2013       Juanjo Menent <jmenent@2byte.es>
 * Copyright (C) 2014       Marcos García <marcosgdf@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *  \file		htdocs/comm/propal/tpl/linkedobjectblock.tpl.php
 *  \ingroup	propal
 *  \brief		Template to show objects linked to proposals
 */

// Protection to avoid direct call of template
if (empty($conf) || !is_object($conf)) {
    print "Error, template page can't be called as URL";
    exit;
}


print "<!-- BEGIN PHP TEMPLATE formulevoyage/core/tpl/linkedobjectblock.tpl.php -->\n";

global $user;

$langs = $GLOBALS['langs'];
$linkedObjectBlock = $GLOBALS['linkedObjectBlock'];

// Load translation files required by the page
$langs->load("formulevoyage");

$linkedObjectBlock = dol_sort_array($linkedObjectBlock, 'date', 'desc', 0, 0, 1);

$total = 0;
$ilink = 0;
foreach ($linkedObjectBlock as $key => $objectlink) {
	$ilink++;

	$trclass = 'oddeven';
	if ($ilink == count($linkedObjectBlock) && empty($noMoreLinkedObjectBlockAfter) && count($linkedObjectBlock) <= 1) {
		$trclass .= ' liste_sub_total';
	}
    if ($objectlink->element == strtolower(Formule::class)){
        $type = $langs->trans("formule");
        $price = $objectlink->tarif;
    }elseif ($objectlink->element == strtolower(Propal::class)){
        $type = $langs->trans("propal");
        $price = $objectlink->total_ht;
    }else{
        $type = $objectlink->element;
        $price = $objectlink->total_ht;
    }
	print '<tr class="'.$trclass.'"  data-element="'.$objectlink->element.'"  data-id="'.$objectlink->id.'" >';
	print '<td class="linkedcol-element tdoverflowmax100">'. $type ;
	print '</td>';
	print '<td class="linkedcol-name tdoverflowmax150">'.$objectlink->getNomUrl(1).'</td>';
	print '<td class="linkedcol-ref" >'.$objectlink->ref_client.'</td>';
	print '<td class="linkedcol-date center">'.dol_print_date($objectlink->date_creation, 'day').'</td>';
	print '<td class="linkedcol-amount right">';
    print price($price). ' €';
	print '</td>';
	print '<td class="linkedcol-statut right">'.$objectlink->getLibStatut(3).'</td>';
	print '<td class="linkedcol-action right"><a class="reposition" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&action=dellink&token='.newToken().'&dellinkid='.$key.'">'.img_picto($langs->transnoentitiesnoconv("RemoveLink"), 'unlink').'</a></td>';
	print "</tr>\n";
}
if (count($linkedObjectBlock) > 1) {
	print '<tr class="liste_total '.(empty($noMoreLinkedObjectBlockAfter) ? 'liste_sub_total' : '').'">';
	print '<td>'.$langs->trans("Total").'</td>';
	print '<td></td>';
	print '<td class="center"></td>';
	print '<td class="center"></td>';
	print '<td class="right">'.price($total).'</td>';
	print '<td class="right"></td>';
	print '<td class="right"></td>';
	print "</tr>\n";
}

print "<!-- END PHP TEMPLATE -->\n";
