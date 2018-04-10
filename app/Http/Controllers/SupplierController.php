<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
// use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ClientRest as R;
use DB;
use App\Supplier;
use App\Order as O;

class SupplierController extends BaseController
{

	/**
	 * Supplier Order Methods
	 */
	public function getAllOrders()
	{
		return O::wsAll();
	}

	public function getAllOpenOrders()
	{
		return O::wsOpen();
	}

	public function getOrder($id)
	{
		return O::wsOne($id);
	}
	
	public function createOrder(Request $r)
	{
		$data 	= $r->all();
		return O::wsCreate($data);
	}

	public function editOrder(Request $r, $id)
	{
		$data 	= $r->all();
		return O::wsEdit($data, $id);
	}

	public function EditPrdOrder(Request $verb,$id_order,$id_product)
	{
		return O::wsEditPrdOrder($verb,$id_order,$id_product);
	}

	public function orderValidated($id_order,$id_supplier)
	{
		return O::wsOrderValidated($id_order,$id_supplier);
	}

	/*
	* Supplier Methods
	*/
	public function getOneSupplier($id)
	{
		try 
		{
			$column = '';
			if (isset($_GET['column'])) {
				$column = $_GET['column'];
			}
			return Supplier::wsfindKey($id,$column);		
		} 
		catch (\Exception $e) 
		{
			return 	[
						'success' 	=>	false,
						'error'		=>	$e->getMessage()
					];	
		}
	}

	public function getAllSupplier()
	{
		try 
		{
			$column = '';
			if (isset($_GET['column'])) {
				$column = $_GET['column'];
			}
			return Supplier::wsAll($column);	
		} 
		catch (\Exception $e) 
		{
			return 	[
						'success' 	=>	false,
						'error'		=>	$e->getMessage()
					];	
		}
	}	

	public function create(Request $verb)
	{
		try 
		{
			return Supplier::wsCreateSupplier($verb);		
		} 
		catch (\Exception $e) 
		{
			return 	[
						'success' 	=>	false,
						'error'		=>	$e->getMessage()
					];	
		}
	}

	public function update(Request $verb,$id)
	{
		try
		{
			return Supplier::wsUpdateSupplier($verb,$id);
		}
		catch(\Exception $e)
		{
			return 	[
						'success'	=> false,
						'error'		=> $e->getMessage()
					];
		}
	}
}
