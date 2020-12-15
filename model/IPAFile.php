<?php
require_once APP_ROOT.'/libs/CFPropertyList/classes/CFPropertyList/CFPropertyList.php';

class IPAFile {

	const PLIST_NAME = '.app/Info.plist';

    protected function unzipMobileProvisionFileName($ipafile)
    {
		$zip = new ZipArchive();
		$r = $zip->open($ipafile);
		if($r!==TRUE){
			throw new RuntimeException(__METHOD__.": ZipArchive::open failed: ".$r);
		}

		try{
			for($i=0;$i<$zip->numFiles;++$i){
				$name = $zip->getNameIndex($i);
				if(mb_ereg_match('^Payload/[^/]*.app/embedded.mobileprovision$',$name)){
					return $zip->getFromIndex($i);
				}
			}
		}
		finally{
			$zip->close();
		}

		throw new RuntimeException(__METHOD__.": embedded.mobileprovision file not found.");
    }

	protected static function unzipInfoPlist($ipafile)
	{
		$zip = new ZipArchive();
		$r = $zip->open($ipafile);
		if($r!==TRUE){
			throw new RuntimeException(__METHOD__.": ZipArchive::open failed: ".$r);
		}

		try{
			for($i=0;$i<$zip->numFiles;++$i){
				$name = $zip->getNameIndex($i);
				if(mb_ereg_match('^Payload/[^/]*.app/Info.plist$',$name)){
					return $zip->getFromIndex($i);
				}
			}
		}
		finally{
			$zip->close();
		}

		throw new RuntimeException(__METHOD__.": Info.plist file not found.");
	}

	public static function parseInfoPlist($ipafile)
	{
		$info_plist = self::unzipInfoPlist($ipafile);
		if($info_plist===FALSE){
			throw new RuntimeException(__METHOD__.": unzipInfoPlist failed.");
		}

		$plutil = new CFPropertyList\CFPropertyList();
		$plutil->parse($info_plist);
		return $plutil->toArray();
	}

    public static function parseMobileProvision($ipafile)
    {
        $profile_name = self::unzipMobileProvisionFileName($ipafile);
        error_log("profile_name: " . $profile_name, 3, "/tmp/parse.log");
        if(!$profile_name){
            throw new UnexpectedValueException(__METHOD__.": embedded.mobiion file not found.");
        }
        $mobile_provison = self::unzipFileAndStrip($ipafile,$profile_name);
        $plutil = new CFPropertyList\CFPropertyList();
        $plutil->parse($mobile_provison);
        return $plutil->toArray();
    }

}
