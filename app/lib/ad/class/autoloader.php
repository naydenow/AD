<?php
/*
Версия файла: 1.1
Автор : Найдёнов Павел
*/
class Autoloader
{
    private static $_lastLoadedFilename;
    public static function loadPackages($className){

    $pathParts = explode('_', $className);

    if (count($pathParts)===1){
    	 $pathParts = explode('\\', $className);
    }

    self::$_lastLoadedFilename = implode(DIRECTORY_SEPARATOR, $pathParts) . '.php';

    //echo ROOT.self::$_lastLoadedFilename.'<br>';
    
    if(file_exists(DIR.self::$_lastLoadedFilename))
        require_once(DIR.self::$_lastLoadedFilename);
    else
    	require_once(ROOT.self::$_lastLoadedFilename);
       // echo ROOT.self::$_lastLoadedFilename.'<br>';
}
}
spl_autoload_register(array('Autoloader', 'loadPackages'));
?>
