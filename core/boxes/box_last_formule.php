<?php

/* Module descriptor for formule system
 * Copyright (C) 2013-2016  Jean-François FERRY <hello@librethic.io>
 * Copyright (C) 2016       Christophe Battarel <christophe@altairis.fr>
 * Copyright (C) 2018-2021  Frédéric France     <frederic.france@netlogic.fr>
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
 *     \file        htdocs/core/boxes/box_last_formule.php
 *     \ingroup     formule
 *     \brief       This box shows latest created formules
 */
require_once DOL_DOCUMENT_ROOT . "/core/boxes/modules_boxes.php";

/**
 * Class to manage the box
 */
class box_last_formule extends ModeleBoxes
{
    public $boxcode = "box_last_formule";
    public $boximg = "formule";
    public $boxlabel;
    public $depends = array("formulevoyage");
    /**
     * @var DoliDB Database handler.
     */
    public $db;
    public $param;
    public $info_box_head = array();
    public $info_box_contents = array();

    /**
     * Constructor
     * @param DoliDB $db Database handler
     * @param string $param More parameters
     */
    public function __construct($db, $param = '') {
        global $langs;
        $langs->load("boxes");
        $this->db = $db;
        $this->enabled = true;
        $this->boxlabel = $langs->transnoentitiesnoconv("labelwidgetform");
    }

    /**
     * Load data into info_box_contents array to show array later.
     *
     * @param int $max Maximum number of records to load
     * @return void
     */
    public function loadBox($max = 5) {
        global $conf, $user, $langs;
        $this->max = $max;

        require_once dirname(__DIR__) . "/../class/formule.class.php";

        $text = $langs->trans("labelwidgetform", $max);
        $this->info_box_head = array(
            'text' => $text,
            'limit' => dol_strlen($text),
        );

        $this->info_box_contents[0][0] = array(
            'td' => 'class="left"',
            'text' => $langs->trans("labelwidgetform"),
        );
        if ($user->rights->formulevoyage->formule->read) {
            $sql = "SELECT t.rowid as id, t.ref, t.date_creation, t.tarif";
            $sql .= " FROM " . MAIN_DB_PREFIX . "formulevoyage_formule as t";
            $sql .= " WHERE t.entity IN (" . getEntity('formule') . ") AND t.status = 1";
            $sql .= " ORDER BY t.rowid DESC";
            $sql .= $this->db->plimit($max, 0);

            $resql = $this->db->query($sql);
            if ($resql) {
                $num = $this->db->num_rows($resql);
                $i = 0;
                while ($i < $num) {
                    $objp = $this->db->fetch_object($resql);
                    $formule = new Formule($this->db);
                    $formule->id = $objp->id;
                    $formule->ref = $objp->ref;
                    $formule->date_creation = $objp->date_creation;
                    $formule->tarif = $objp->tarif;
                    $formule->status = $objp->status;
                    $r = 0;

                    // formule
                    $this->info_box_contents[$i][$r] = array(
                        'td' => 'class="nowraponall"',
                        'text' => $formule->getNomUrl(1),
                        'asis' => 1
                    );
                    $r++;
                    $this->info_box_contents[$i][$r] = array(
                        'td' => 'class="nowraponall"',
                        'text' => dol_print_date($formule->date_creation, '%d/%m/%Y'),
                        'asis' => 1
                    );
                    $r++;
                    $this->info_box_contents[$i][$r] = array(
                        'td' => 'class="nowraponall"',
                        'text' => price($formule->tarif) . '€',
                        'asis' => 1
                    );
                    $i++;
                }

            } else {
                dol_print_error($this->db);
            }
        } else {
            $this->info_box_contents[0][0] = array('td' => '',
                'text' => '<span class="opacitymedium">' . $langs->trans("ReadPermissionNotAllowed") . '</span>');
        }
    }

    /**
     *     Method to show box
     *
     * @param array $head Array with properties of box title
     * @param array $contents Array with properties of box lines
     * @param int $nooutput No print, only return string
     * @return string
     */
    public function showBox($head = null, $contents = null, $nooutput = 0) {
        return parent::showBox($this->info_box_head, $this->info_box_contents, $nooutput);
    }
}
