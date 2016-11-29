<?php
class WPML_ACF_Post_Ids implements WPML_ACF_Convertable {
	public function convert( WPML_ACF_Field $WPML_ACF_Field) {
		
		$ids_unpacked = (array) maybe_unserialize($WPML_ACF_Field->meta_value);
		
		$ids = array();
		foreach ($ids_unpacked as $id) {
			$ids[] = new WPML_ACF_Post_Id($id, $WPML_ACF_Field);
		}
		
		$ids_converted_object = array_map(function($id) {return $id->convert();}, $ids);
		
		foreach ($ids_converted_object as $id_object) {
			$result[] = $id_object->id;
		}
		
		if (count($result) == 1) {
			return $result[0];
		} 
		
		return maybe_serialize($result);
		
	}
}
