<?php 
namespace App;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelGetProperties;
use Illuminate\Http\Request;
use DB;

class Supplier extends Model {

	protected $table 			= 'apb_suppliers';
	protected $primaryKey 		= 'id_supplier';
	public $timestamps 			= false;
	// public $editable 		= 	['name', 'key'];

	use ModelGetProperties;
	/**
	*ws Method
	*/
	public static function wsOne($id)
	{
		//
		if(!self::find($id))
		{
			return [];
		}

		$one = self::find($id);
		return  [
					'id' 	=> $one->id_supplier,
					'name'	=> $one->supplier_name,
					'key'	=> $one->key
				];
	}

	public static function wsAll()
	{
		//
		$all 	= [];
		$result = self::all();
		if(count($result)  == 0)
		{
			return $all;
		}

		foreach($result as $one)
		{
			$all[] = 	[
							'id' 	=> $one->id_supplier,
							'name'	=> $one->supplier_name,
							'key'	=> $one->key
					 	];
		}

		return $all;
	}

	public static function wsfindKey($id)
	{
		//
		if(strlen($id) < 4) 
		{
			 	return self::wsOne($id);
		}
		else 
		{
			 	return self::findKey($id);		
		}
	}

	public static function wsCreateSupplier($verb)
	{
		try {
				if(!$verb->has('name') || !$verb->has('key'))
				{
					return ['success'	=>	FALSE, 'error'	=>	'name, key fields can not be empty'];
				}

				$data = [
						'supplier_name' => $verb->input('name'), 
						'key' => $verb->input('key')
						];

				$res = DB::table(self::getProp('table'))
					->where('supplier_name', '=' , $data['supplier_name'])
					->orWhere('key', '=' , $data['key'])
					->first();
														
				if(!empty($res)) 
				{
					return ['success'	=> FALSE, 'error'	=> 'supplier already exit'];
				}
				else 
				{
					$last_id = DB::table(self::getProp('table'))->insertGetId($data);
					return ['success' => TRUE,'id' => $last_id];
				}
			} catch (\Exception $e) {
				return ['success' => FALSE, 'error' => $e->getMessage() ];
			}
	}


	public static function wsUpdateSupplier($verb,$id)
	{
		try {
				$res = self::wsOne($id);

				if(!empty($res)) 
				{
					$editable 	= 	['name', 'key'];
					$error 		= 	FALSE;
					$input 		= 	$verb->all();

					foreach($input as $k => $v)
					{
						if (!in_array($k, $editable)) {
							$error = TRUE;
						}
					}

					if(!$error)
					{
						$data 	= 	[
										'supplier_name' =>	$verb->input('name'),
										'key'			=>	$verb->input('key')
									];
						DB::table(self::getProp('table'))
						->where('id_supplier',$id)->update($data);

						return [ 'success'	=> TRUE ];
					}
					else 
					{
						return [	
								'success' 			=> FALSE, 
								'error' 			=> 'The following fields must be provided : '.implode(" , ",$editable)
								];
					}

				}
				else
				{
					return ['success'	=> FALSE,	'error'	=> 'supplier not found' ];
				}

		} catch (\Exception $e) {
			return 	[
						'success' => FALSE, 
						'error' => $e->getMessage() 
					];
		}
	}
	
	/*
	*Public Method
	*/
	public static function findKey($id)
	{
		//
		$suppliers = self::where('key', '=', $id)->first();
		if (!$suppliers) 
		{
			return [];
		}
		return Supplier::wsOne($suppliers->id_supplier);
	}
}