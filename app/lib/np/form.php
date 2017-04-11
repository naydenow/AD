<?php
namespace np;
class form {
	static public function load($modul,$form)
	{
		include_once( ROOT.AD::config('templatepath').$this->tpname.".php");

	}
	static public function template($data,$tpname)
	{


		$tp = ROOT.\AD::config('templatepath').$tpname.".tpl";
		if(!file_exists($tp)){
			$tp = ROOT.'/'.$tpname;
		}

		$content = file_get_contents($tp);
		foreach ($data as $key => $var) {
			$content = str_replace('{%'.$key.'%}', $var, $content);
		}
		echo $content;
	}
}