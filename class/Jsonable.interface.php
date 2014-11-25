<?php
	
	/**
	 * Transforme un objet en array qui pourra être parsé en json
	 */
	interface Jsonable {

		public function toJson();

		public static function fromJson();
	}
?>