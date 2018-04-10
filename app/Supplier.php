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
	public static $showable    	= '';

	use ModelGetProperties;
	/**
	*ws Method
	*/
	public static function wsOne($id,$column='')
	{
		if(!self::find($id))
		{
			return [];
		}

		if ($column) 
		{	
	  		self::setColumn($column);
		}
    	return self::remapSupplierAttributes($id);
	}

	public static function wsAll($column='')
	{
		$result = self::all();
		if(count($result)  == 0)
		{
			return [];
		}

		foreach($result as $one)
		{
			$all[] = self::wsOne($one->id_supplier);
		}
		return $all;
	}

	public static function wsfindKey($id,$column='')
	{
		//
		if(strlen($id) < 4) 
		{
			 	return self::wsOne($id,$column);
		}
		else 
		{
			 	return self::findKey($id,$column);		
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
	public static function findKey($id,$column)
	{
		//
		$suppliers = self::where('key', '=', $id)->first();
		if (!$suppliers) 
		{
			return [];
		}
		return Supplier::wsOne($suppliers->id_supplier,$column);
	}

	/*
	*Internal method
	*/

    public static function setColumn($raw)
    {
        if($raw)
        {
            $selected       = explode(',', $raw);
            self::$showable = $selected;
        }
    }

    public static function remapSupplierAttributes($id,$display = [])
    {
    	if (empty($display)) 
    	{
    		$display	=	[
    							'id',
    							'name',
    							'email',
    							'key'
    						];
    	}

    	$selected = self::$showable;
    	if(!empty($selected))
        {
            $display = $selected;
        }
		// $data 	=	[];
		$one 		= 	self::find($id);						
			$data['id'] = 	$one->id_supplier;

		    if(in_array('name', $display))
		        $data['name'] 	= 	$one->supplier_name;

		   	if(in_array('key', $display))
		        $data['key'] 	= 	$one->key;

		    if(in_array('email', $display))
		        $data['email'] 	= 	$one->email;
	     	
	    return $data;   
    }
}