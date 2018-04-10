<?php 
namespace App;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelGetProperties;
use Illuminate\Http\Request;
use DB;
use App\Supplier;

class Order extends Model {

	protected $table_order			= 'apb_orders';
	protected $table_prd_order 		= 'apb_prd_order';
	protected $table_supplier 		= 'apb_suppliers';
	protected $table_transporter 	= 'apb_prd_ordertransporter';
	protected $primaryKey 			= 'id_order';

	use ModelGetProperties;
	
	/**
	 * ws Method
	 */
	public static function wsAll()
	{
		try 
		{
			return self::getAll();
		}
		catch(\Exception $e)
		{
			return 	[
				'success'	=> false,
				'error'		=> $e->getMessage()
					];
		}
	}
	
	public static function wsOpen()
	{
		try 
		{
			return self::getAll(true);
		}
		catch(\Exception $e)
		{
			return [
				'success'	=> false,
				'error'		=> $e->getMessage()
			];
		}
	}
	
	public static function wsOne($id)
	{
		try 
		{
			return self::getById($id);
		}
		catch(\Exception $e)
		{
			return [
				'success'	=> false,
				'error'		=> $e->getMessage()
			];
		}
	}
	
	public static function wsCreate($data)
	{
		try 
		{
			$fillable 	= ['id_supplier', 'id_transporter', 'created_by', 'test_unit'];
			foreach($data as $key => $value)
			{
				if(!in_array($key, $fillable))
				{
					throw new \Exception('Wrong posted data : ' . $key);
				}
				else
				{
					if(intval($value)==0 && $key != 'test_unit')
					{
						throw new \Exception('Required field : ' . $key);
					}
				}
			}
			return self::createOrder($data);
		}
		catch(\Exception $e)
		{
			return [
				'success'	=> false,
				'error'		=> $e->getMessage()
			];
		}
	}
	
	public static function wsEdit($data, $id)
	{
		try 
		{
			$fillable 	= [
				'id_supplier', 
				'id_transporter', 
				'created_by',
				'transporter_price',
			    'totalweight',
			    'tracking',
			    'step',
			    'status',
			    'processed',
			    'refund',
			    'packaging',
			    'full_cost_computed',
			    'test_unit'
			];
			foreach($data as $key => $value)
			{
				if(!in_array($key, $fillable))
				{
					throw new \Exception('Wrong posted data : ' . $key);
				}
				else
				{
					if(substr($value, 0, 3) == 'id_')
					{
						if(intval($value)==0)
						{
							throw new \Exception('Required field : ' . $key);
						}
					}
				}
			}
			return self::editOrder($data, $id);
		}
		catch(\Exception $e)
		{
			return [
				'success'	=> false,
				'error'		=> $e->getMessage()
			];
		}
	}

	public static function wsEditPrdOrder($verb,$id_order,$id_product)
	{	
		try 
		{
			$editable 	= 	['buying_price', 'qty_wanted', 'status', 'target_price'];
	    	$input		=	$verb->all();
			$error		= 	false;    	
	    	foreach ($input as $k => $v) 
	    	{
	    		if (!in_array($k, $editable)) 
	    		{
	    			$error = true;
	    		}
	    	}
	    	if (!$error) 
	    	{
	    		DB::table(self::getProp('table_prd_order'))
					->where('id_order', $id_order)
					->where('id_product', $id_product)
					->update($input);
				return 	[
							'success' 	=> true
						];
	    	} 
	    	else
	    	{
	    		return 	[
	    					'success'	=>	false,
	    					'error'		=>	'You must provide at least one of there: '. implode(', ', $editable)
	    				];
	    	}
	    	
		} catch (\Exception $e) {
			return 	[
						'success' => FALSE, 
						'error' => $e->getMessage() 
					];
		}
	}

	public static function wsOrderValidated($id_order,$id_supplier)
	{
		try 
		{
			$result = DB::table(self::getProp('table_prd_order'))
			->select('id_order','status','id_product')
			->where('id_order','=',$id_order )
			->get();

			foreach ($result as $one) 
			{
				if ($one->status != 'ok') {
					DB::table(self::getProp('table_prd_order'))
					->where('id_product', '=', $one->id_product)
					->delete();
					// DB::table(self::getProp('table_prd_order'))
					// 		->where('id_product', '=', $one->id_product)
					// 		->update(['status'=>'']);
				}
			}
			DB::table(self::getProp('apb_orders'))
							->where('id_order', '=', $id_order)
							->where('id_supplier', '=', $id_supplier)
							->update(['status'=>1]);
			return 	[
						'success'	=> true 
					];
		} catch (\Exception $e) {
			return 	[
						'success' => FALSE, 
						'error' => $e->getMessage() 
					];
		}
	}

	/**
	 * Internal Method
	 */
	public static function getAll($open = false)
	{
		try 
		{
			$data = DB::table(self::getProp('table_order'));
			if($open)
			{
				$data->where('step','<',6);
			}
			$orders = $data->get();
			$result = [];
			foreach($orders as $o)
			{
				$tmp = [];
				if($open)
				{
					$montant = 0;
					$lc = DB::table(self::getProp('table_prd_order'))
							->selectRaw(
								'id_product,
								buying_price AS buyingprice,
								qty_wanted,
								qty_shipped,
								format(buying_price * qty_wanted,2) AS montant'
							)
							->where('id_order', $o->id_order)
							->get();
					foreach($lc as $c)
					{
						if($o->step < 5)
						{
							$montant += ($c->qty_wanted * $c->buyingprice);
						}
						else 
						{
							$montant += ($c->qty_shipped * $c->buyingprice);
						}
					}
					$tmp['montant'] = $montant;
				}
				$supplier = (array)Supplier::wsOne($o->id_supplier);
				unset($supplier['key']);
				$tmp = array_merge([
					'id'					=> $o->id_order,
					'supplier'				=> $supplier,
					'transporter' 			=> (array) DB::table(self::getProp('table_transporter'))
												->selectRaw('id_transporter AS id, name')
												->where('id_transporter', $o->id_transporter)
												->first(),
					'transporter_price'		=> $o->transporter_price,
					'totalweight'			=> $o->totalweight,
					'step'					=> $o->step,
					'status'				=> $o->status,
					'date_added'			=> $o->date_added
				],$tmp);
				$result[] = $tmp;
			}
			return $result;
		}
		catch(\Exception $e)
		{
			die(var_dump($e));
			throw $e;
		}
	}
	
	public static function getById($idorder)
	{
		try 
		{
			$o = DB::table(self::getProp('table_order'))
				->where('id_order', $idorder)
				->first();
			if($o)
			{
				$supplier = (array)Supplier::wsOne($o->id_supplier);
				unset($supplier['key']);
				$result 	= [
					'id'					=> $o->id_order,
					'supplier'				=> $supplier,
					'transporter' 			=> (array) DB::table(self::getProp('table_transporter'))
												->selectRaw('id_transporter AS id, name')
												->where('id_transporter', $o->id_transporter)
												->first(),
					'transporter_price'		=> $o->transporter_price,
					'totalweight'			=> $o->totalweight,
					'step'					=> $o->step,
					'status'				=> $o->status,
					'date_added'			=> $o->date_added
				];
				$carts 		= [];
				$carts 		= DB::table(self::getProp('table_prd_order'))
								->where('id_order', $idorder)
								->get();
				$carts 		= json_decode(json_encode($carts),true);
				$total 		= 0;
				$dontshow 	= ['id_order','parcel_number','qty_wanted','qty_shipped','qty_received','qty_ok','qty_nok','qty_knok', 'full_cost'];
				foreach($carts as $key => $c)
				{
					$qty = 0;
					if($o->step < 5)
					{
						$carts[$key]['montant'] = ($c['qty_wanted'] * $c['buying_price']);
						$total += ($c['qty_wanted'] * $c['buying_price']);
						$qty = $c['qty_wanted'];
					}
					else if ($o->step == 5) 
					{
						$carts[$key]['montant'] = ($c['qty_shipped'] * $c['buying_price']);
						$total += ($c['qty_shipped'] * $c['buying_price']);
						$qty = $c['qty_shipped'];
					}
					else 
					{
						$carts[$key]['montant'] = ($c['qty_shipped'] * $c['buying_price']);
						$total += ($c['qty_shipped'] * $c['buying_price']);
						$qty = $c['qty_received'];
					}
					$carts[$key]['qty'] = $qty;
					foreach($dontshow as $ds)
					{
						if(isset($carts[$key][$ds]))
						{
							unset($carts[$key][$ds]);
						}
					}
				}
				$result['montant_total'] 	= $total;
				$result['cart'] 			= $carts;
				return $result;
			}
			return [];
		}
		catch(\Exception $e)
		{
			throw $e;
		}
	}
	
	public static function createOrder($data)
	{	
		try 
		{
			DB::beginTransaction();
			$doublon = DB::table(self::getProp('table_order'))
				->where('step',1)
				->where('id_supplier', $data['id_supplier'])
				->first();
			if($doublon)
			{
				throw new \Exception("another order " . $doublon->id_order . " at step 1 already exists for this supplier");
			}
			$data = array_merge($data, [
			    'transporter_price' 	=> 0,
			    'totalweight' 			=> 0,
			    'tracking' 				=> '',
			    'step' 					=> 1,
			    'status'				=> 1,
			    'processed' 			=> 0,
			    'refund' 				=> 0,
			    'date_added' 			=> new \DateTime(),
			    'date_updated' 			=> new \DateTime(),
			    'packaging' 			=> 0,
			    'full_cost_computed' 	=> 0
			]);
			$testunit = isset($data['test_unit']);
			if($testunit)
			{
				unset($data['test_unit']);
			}
			$id = DB::table(self::getProp('table_order'))
				->insertGetId($data);
			if($id)
			{
				if($testunit)
				{
					DB::table(self::getProp('table_order'))
						->where('id_order', $id)
						->delete();
				}
				DB::commit();
				$data['id_order'] = $id;
				return [
					'success' 	=> true,
					'error'		=> '',
					'data'		=> $data
				];
			}
			else
			{
				throw new \Exception("Une erreur est survenue lors de l'insertion de la commande");
			}
		}
		catch(\Exception $e)
		{
			DB::rollback();
			throw $e;
		}
	}
	
	public static function editOrder($data, $id)
	{	
		try 
		{
			DB::beginTransaction();
			$o = self::wsOne($id);
			if(!$o)
			{
				throw new \Exception("Required field : id_order");
			}
			if(isset($data['id_supplier']))
			{
				$sup 	= $data['id_supplier'];
				$double = DB::table(self::getProp('table_order'))
							->whereRaw('(step = 1 or status = 1) and id_supplier <> ' . $sup)
							->get();
				foreach($double as $d)
				{
					if($id != $d->id_order && $sup == $d->id_supplier)
					{
						throw new \Exception("another order " . $d->id_order . " at step 1 / status 1 already exists for this supplier");
					}
				}
			}
			else
			{
				throw new \Exception("Supplier is not set");
			}

			$data = array_merge($data, [
			    'date_updated' 			=> new \DateTime()
			]);
			$testunit = isset($data['test_unit']);
			if($testunit)
			{
				unset($data['test_unit']);
				$id = DB::table(self::getProp('table_order'))
						->insertGetId($data);
				DB::table(self::getProp('table_order'))
					->where('id_order', $id)
					->update($data);
				DB::table(self::getProp('table_order'))
					->where('id_order', $id)
					->delete();
			}
			else
			{
				DB::table(self::getProp('table_order'))
					->where('id_order', $id)
					->update($data);
			}
			DB::commit();
			$data['id_order'] = $id;
			return [
				'success' 	=> true,
				'error'		=> '',
				'data'		=> $data
			];
		}
		catch(\Exception $e)
		{
			DB::rollback();
			throw $e;
		}
	}
	
}