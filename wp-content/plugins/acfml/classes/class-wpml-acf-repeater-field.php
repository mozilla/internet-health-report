<?php

class WPML_ACF_Repeater_Field {
	private $duplicated_post_object;

	public function __construct(&$duplicated_post_object)
	{
		$this->duplicated_post_object = $duplicated_post_object;
	}

	public function resolve_repeater_subfield($processed_data, $key_parts, $field) {
		$repeater_field = get_post_meta($processed_data->meta_data['master_post_id'], "_" . $key_parts[1], true);

		if ($repeater_field) {
			$value = $this->duplicated_post_object->get_related_acf_field_value($repeater_field);
			if ('repeater' == $value['type'] && isset($value['sub_fields'])) {
				foreach ($value['sub_fields'] as $key => $sub_field) {
					if (isset($sub_field['name']) && $sub_field['name'] == $key_parts[3] && isset($sub_field['type'])) {
						$processed_data->related_acf_field_value = $sub_field['type'];
						$field = $this->duplicated_post_object->get_field_object($processed_data, $field);
						break;
					}
				}
			}
		}

		return $field;
	}
}