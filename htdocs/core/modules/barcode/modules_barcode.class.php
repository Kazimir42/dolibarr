<?php
/* Copyright (C) 2014 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 * or see https://www.gnu.org/
 */

/**
 *   \file       htdocs/core/modules/barcode/modules_barcode.class.php
 *   \ingroup    barcode
 *   \brief      File with parent classes for barcode document modules and numbering modules
 */
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/commonnumrefgenerator.class.php';


/**
 *	Parent class for barcode document models
 */
abstract class ModeleBarCode
{
	/**
	 * @var string Error code (or message)
	 */
	public $error = '';


	/**
	 * Return if a model can be used or not
	 *
	 * @return		boolean     true if model can be used
	 */
	public function isEnabled()
	{
		return true;
	}
}


/**
 *	Parent class for barcode numbering models
 */
abstract class ModeleNumRefBarCode extends CommonNumRefGenerator
{

	/**
	 * @var int Code facultatif
	 */
	public $code_null;

	/**
	 * @var int Automatic numbering
	 */
	public $code_auto;


	/**
	 *  Return next value available
	 *
	 *	@param	Product		$objproduct	Object Product
	 *	@param	string		$type		Type of barcode (EAN, ISBN, ...)
	 *  @return string      			Value
	 */
	public function getNextValue($objproduct, $type = '')
	{
		global $langs;
		return $langs->trans("Function_getNextValue_InModuleNotWorking");
	}

	/**
	 *      Return description of module parameters
	 *
	 *      @param	Translate	$langs      Output language
	 *		@param	Societe		$soc		Third party object
	 *		@param	int			$type		-1=Nothing, 0=Product, 1=Service
	 *		@return	string					HTML translated description
	 */
	public function getToolTip($langs, $soc, $type)
	{
		global $conf;

		$langs->loadLangs(array("admin", "companies"));

		$s = '';
		$s .= $langs->trans("Name").': <b>'.$this->name.'</b><br>';
		$s .= $langs->trans("Version").': <b>'.$this->getVersion().'</b><br>';
		if ($type != -1) {
			$s .= $langs->trans("ValidityControledByModule").': <b>'.$this->getNom($langs).'</b><br>';
		}
		$s .= '<br>';
		$s .= '<u>'.$langs->trans("ThisIsModuleRules").':</u><br>';
		if ($type == 0) {
			$s .= $langs->trans("RequiredIfProduct").': ';
			if (!empty($conf->global->MAIN_BARCODE_CODE_ALWAYS_REQUIRED) && !empty($this->code_null)) {
				$s .= '<strike>';
			}
			$s .= yn(!$this->code_null, 1, 2);
			if (!empty($conf->global->MAIN_BARCODE_CODE_ALWAYS_REQUIRED) && !empty($this->code_null)) {
				$s .= '</strike> '.yn(1, 1, 2).' ('.$langs->trans("ForcedToByAModule", $langs->transnoentities("yes")).')';
			}
			$s .= '<br>';
		}
		if ($type == 1) {
			$s .= $langs->trans("RequiredIfService").': ';
			if (!empty($conf->global->MAIN_BARCODE_CODE_ALWAYS_REQUIRED) && !empty($this->code_null)) {
				$s .= '<strike>';
			}
			$s .= yn(!$this->code_null, 1, 2);
			if (!empty($conf->global->MAIN_BARCODE_CODE_ALWAYS_REQUIRED) && !empty($this->code_null)) {
				$s .= '</strike> '.yn(1, 1, 2).' ('.$langs->trans("ForcedToByAModule", $langs->transnoentities("yes")).')';
			}
			$s .= '<br>';
		}
		if ($type == -1) {
			$s .= $langs->trans("Required").': ';
			if (!empty($conf->global->MAIN_BARCODE_CODE_ALWAYS_REQUIRED) && !empty($this->code_null)) {
				$s .= '<strike>';
			}
			$s .= yn(!$this->code_null, 1, 2);
			if (!empty($conf->global->MAIN_BARCODE_CODE_ALWAYS_REQUIRED) && !empty($this->code_null)) {
				$s .= '</strike> '.yn(1, 1, 2).' ('.$langs->trans("ForcedToByAModule", $langs->transnoentities("yes")).')';
			}
			$s .= '<br>';
		}
		/*$s.=$langs->trans("CanBeModifiedIfOk").': ';
		$s.=yn($this->code_modifiable,1,2);
		$s.='<br>';
		$s.=$langs->trans("CanBeModifiedIfKo").': '.yn($this->code_modifiable_invalide,1,2).'<br>';
		*/
		$s .= $langs->trans("AutomaticCode").': '.yn($this->code_auto, 1, 2).'<br>';
		$s .= '<br>';

		$nextval = $this->getNextValue($soc, '');
		if (empty($nextval)) {
			$nextval = $langs->trans("Undefined");
		}
		$s .= $langs->trans("NextValue").': <b>'.$nextval.'</b><br>';

		return $s;
	}
}
