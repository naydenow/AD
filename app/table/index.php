<?php
/*

*/
class table_autocompiller extends ad_table {

  //Выодим все совпадения
  public function getcityToStr($str){
    return $this->getAll("SELECT *
                          from table
                          where field like ?s limit 10",$str.'%');
  }
}