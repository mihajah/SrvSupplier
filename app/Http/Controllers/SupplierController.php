<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
// use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ClientRest as R;
use DB;
use App\Supplier;

class SupplierController extends BaseController
{

	public function getAllOrders()
	{
		try
		{
			$url 	= config('constants.api.url').'suppliers/orders';
			return R::get($url);
		}

		catch(\Exception $e)
		{
			return [
				'success'	=> false,
				'error'		=> $e->getMessage()
			];
		}
	}

	public function getAllOpenOrders()
	{
		try
		{
			$url 	= config('constants.api.url').'suppliers/orders/open';
			return R::get($url);
		}
		catch(\Exception $e)
		{
			return [
				'success'	=> false,
				'error'		=> $e->getMessage()
			];
		}
	}

	public function getOrder($id)
	{
		try
		{
			$id 	= (int) $id;
			$url 	= config('constants.api.url').'suppliers/order/'.$id;
			return R::get($url);
		}
		catch(\Exception $e)
		{
			return [
				'success'	=> false,
				'error'		=> $e->getMessage()
			];
		}
	}

	public function createOrder(Request $r)
	{
		try
		{
			$url 	= config('constants.api.url').'suppliers/createorder';
			$data 	= $r->all();
			return R::post($url, $data);
		}
		catch(\Exception $e)
		{
			return [
				'success'	=> false,
				'error'		=> $e->getMessage()
			];
		}
	}

	public function editOrder(Request $r)
	{
		try
		{
			$url 	= config('constants.api.url').'suppliers/editorder';
			$data 	= $r->all();
			return R::put($url, $data);
		}
		catch(\Exception $e)
		{
			return [
				'success'	=> false,
				'error'		=> $e->getMessage()
			];
		}
	}

	/*
	* Method for Supplier
	*/
	public function getOneSupplier($id)
	{
		try 
		{
			return Supplier::wsfindKey($id);		
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
			return Supplier::wsAll();	
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
