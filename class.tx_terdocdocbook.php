<?php

/***************************************************************
*  Copyright notice
*
*  (c) 2005 Robert Lemke (robert@typo3.org)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Docbook download option for TER Doc 
 *
 * $Id$
 *
 * @author	Robert Lemke <robert@typo3.org>
 */

class tx_terdocdocbook {

	/**
	 * Renders the cache for a DocBook download version. The result consists
	 * of a zipped docbook file including images
	 * 
	 * @param	string		$documentDir: Absolute directory for the document currently being processed.
	 * @return	void		
	 * @access	public
	 */
	public function renderCache ($documentDir) {
		$staticConfArr = unserialize ($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ter_doc_docbook']);
		if (is_array ($staticConfArr)) {		
			$zipCommand = $staticConfArr['zipCommand'];

				// zip the DocBook files:
			$zipCommand = str_replace ('###ARCHIVENAME###', $documentDir.'docbook/manual.zip', $zipCommand);
			$zipCommand = str_replace ('###DIRECTORY###', $documentDir.'docbook/', $zipCommand);
			exec($zipCommand);
			
		} else {
			return FALSE;	
		}
	}

	/**
	 * Returns TRUE if a rendered document for the given extension version is
	 * available.
	 * 
	 * @param	string		$extensionKey: Extension key of the document
	 * @param	string		$version: Version number of the document
	 * @return	boolean		TRUE if rendered version is available, otherwise FALSE		
	 * @access	public
	 */
	public function isAvailable ($extensionKey, $version) {
		$documentDir = $this->getDocumentDirOfExtensionVersion ($extensionKey, $version);
		return @is_file ($documentDir.'docbook/manual.zip');
	}

	/**
	 * Returns the download size in bytes
	 * 
	 * @param	string		$extensionKey: Extension key of the document
	 * @param	string		$version: Version number of the document
	 * @return	integer		The download size		
	 * @access	public
	 */
	public function getDownloadFileSize ($extensionKey, $version) {
		$documentDir = $this->getDocumentDirOfExtensionVersion ($extensionKey, $version);
		return @filesize ($documentDir.'docbook/manual.zip');
	}





	
	/**
	 * Returns the full path of the document directory for the specified
	 * extension version. If the path does not exist yet, it will be created - 
	 * given that the typo3temp/tx_terdoc/documentscache/ dir exists.  
	 * 
	 * In the document directory all rendered documents are stored.
	 * 
	 * @param	string		$extensionKey: The extension key
	 * @param	string		$version: The version string
	 * @return	string		Full path to the document directory for the specified extension version
	 */
	protected function getDocumentDirOfExtensionVersion ($extensionKey, $version) {
		$firstLetter = strtolower (substr ($extensionKey, 0, 1));
		$secondLetter = strtolower (substr ($extensionKey, 1, 1));
		$baseDir = PATH_site.'typo3temp/tx_terdoc/documentscache/';

 		list ($majorVersion, $minorVersion, $devVersion) = t3lib_div::intExplode ('.', $version);
		$fullPath = $baseDir.$firstLetter.'/'.$secondLetter.'/'.strtolower($extensionKey).'-'.$majorVersion.'.'.$minorVersion.'.'.$devVersion;
						
		return $fullPath.'/';		
	}
	
}
?>