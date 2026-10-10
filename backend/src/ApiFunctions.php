<?php
namespace Jenniferd/NutritionApi;

class ApiFunctions{
    public static class arrayToQueryArray($arr){}

    public static class arrayToWildCardArray(array $arr): string{
        $query = '';
        foreach ($arr as $item){
            $query .= "'%$item%', ";
        }
        $query = '[' . substr($query, 0, mb_strlen($query)) . ']';
        return $query;
    }
}
?>