<?php

/**
 * @SWG\Swagger(
 *     schemes={"http", "https"},
 *     host="staging.touchiz.fr",
 *     basePath="/dev/services",
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="Techtablet Services Documentation",
 *         description="micro services",
 *     )
 * )
 */
use Illuminate\Support\Facades\App;
use Swagger\Swagger;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/



$app->get('/', function () use ($app) {
    return $app->welcome();
});


// ROUTES FOR SUPPLIER ORDERS

    
/**
 * @SWG\Get(
 *     path="/supplierorders",
 *     tags={"Gestion des commandes fournisseur"},
 *     summary="Toutes les commandes",
 *     description="Liste de toutes les commandes fournisseur",
 *     operationId="getOrders",
 *     produces={"application/json"},
 *     @SWG\Response(
 *         response=500,
 *         description="Echec de la requête"
 *     ),
 *     @SWG\Response(
 *         response=404,
 *         description="Ressource introuvable"
 *     ),
 *     @SWG\Response(
 *          response="200", 
 *          description="Connexion avec le serveur avec succès")
 * )
 */
$app->get('/supplierordersz', 'SupplierController@getAllOrders');


/**
 * @SWG\Get(
 *     path="/supplierorders/open",
 *     tags={"Gestion des commandes fournisseur"},
 *     summary="Toutes les commandes en cours de préparation",
 *     description="Récupération des commandes fournisseur en cours de préparation",
 *     operationId="getOpenOrders",
 *     produces={"application/json"},
 *     @SWG\Response(
 *         response=500,
 *         description="Echec de la requête"
 *     ),
 *     @SWG\Response(
 *         response=404,
 *         description="Ressource introuvable"
 *     ),
 *     @SWG\Response(
 *          response="200", 
 *          description="Connexion avec le serveur avec succès")
 * )
 */
$app->get('/supplierorders/open', 'SupplierController@getAllOpenOrders');


/**
 * @SWG\Get(
 *     path="/supplierorders/{id_order}",
 *     tags={"Gestion des commandes fournisseur"},
 *     summary="Détail d'une commande",
 *     description="Récupération d'une commande fournisseur au moyen de l'ID de la commande passé en paramètre",
 *     operationId="getSupplierOrder",
 *     produces={"application/json"},
 *     @SWG\Parameter(
 *         name="id_order",
 *         in="path",
 *         description="ID de la commande",
 *         required=true,
 *         type="integer"
 *     ),
 *     @SWG\Response(
 *         response=500,
 *         description="Echec de la requête"
 *     ),
 *     @SWG\Response(
 *         response=404,
 *         description="Ressource introuvable"
 *     ),
 *     @SWG\Response(
 *          response="200", 
 *          description="Connexion avec le serveur avec succès")
 * )
 */
$app->get('/supplierorders/{id}', 'SupplierController@getOrder');


/**
 * @SWG\Post(
 *     path="/supplierorders",
 *     tags={"Gestion des commandes fournisseur"},
 *     summary="Création d'une commande",
 *     description="Création d'une commande fournisseur au moyen des informations passées en paramètre",
 *     operationId="createSupplierOrder",
 *     produces={"application/json"},
 *     @SWG\Parameter(
 *         name="id_supplier",
 *         in="query",
 *         description="ID du fournisseur",
 *         required=true,
 *         type="integer"
 *     ),
 *     @SWG\Parameter(
 *         name="id_transporter",
 *         in="query",
 *         description="ID du transporteur",
 *         required=true,
 *         type="integer"
 *     ),
 *     @SWG\Parameter(
 *         name="created_by",
 *         in="query",
 *         description="ID de l'application",
 *         required=true,
 *         type="integer"
 *     ),
 *     @SWG\Response(
 *         response=500,
 *         description="Echec de la requête"
 *     ),
 *     @SWG\Response(
 *         response=404,
 *         description="Ressource introuvable"
 *     ),
 *     @SWG\Response(
 *          response="200", 
 *          description="Connexion avec le serveur avec succès")
 * )
 */
$app->post('/supplierorders', 'SupplierController@createOrder');


/**
 * @SWG\Put(
 *     path="/supplierorders/{id_order}",
 *     tags={"Gestion des commandes fournisseur"},
 *     summary="Edition d'une commande",
 *     description="Edition d'une commande fournisseur au moyen des informations passées en paramètre",
 *     operationId="editSupplierOrder",
 *     produces={"application/json"},
 *     @SWG\Parameter(
 *         name="id_order",
 *         in="path",
 *         description="ID de la commande",
 *         required=true,
 *         type="integer"
 *     ),
 *     @SWG\Parameter(
 *         name="id_supplier",
 *         in="query",
 *         description="ID du fournisseur",
 *         required=true,
 *         type="integer"
 *     ),
 *     @SWG\Parameter(
 *         name="id_transporter",
 *         in="query",
 *         description="ID du transporteur",
 *         required=true,
 *         type="integer"
 *     ),
 *     @SWG\Parameter(
 *         name="created_by",
 *         in="query",
 *         description="ID de l'application",
 *         required=true,
 *         type="integer"
 *     ),
 *     @SWG\Parameter(
 *         name="transporter_price",
 *         in="query",
 *         description="Frais de transport",
 *         type="number",
 *         default=0
 *     ),
 *     @SWG\Parameter(
 *         name="totalweight",
 *         in="query",
 *         description="Poids total de la commande",
 *         type="number",
 *         default=0
 *     ),
 *     @SWG\Parameter(
 *         name="tracking",
 *         in="query",
 *         description="Tracking",
 *         type="string",
 *         default=""
 *     ),
 *     @SWG\Parameter(
 *         name="step",
 *         in="query",
 *         description="Status actuel de la commande",
 *         type="integer",
 *         default=1
 *     ),
 *     @SWG\Parameter(
 *         name="processed",
 *         in="query",
 *         description="Processed",
 *         type="integer",
 *         default=0
 *     ),
 *     @SWG\Parameter(
 *         name="refund",
 *         in="query",
 *         description="Refund",
 *         type="integer",
 *         default=0
 *     ),
 *     @SWG\Parameter(
 *         name="packaging",
 *         in="query",
 *         description="Packaging",
 *         type="integer",
 *         default=0
 *     ),
 *     @SWG\Parameter(
 *         name="full_cost_computed",
 *         in="query",
 *         description="Revient",
 *         type="number",
 *         default=0
 *     ),
 *     @SWG\Response(
 *         response=500,
 *         description="Echec de la requête"
 *     ),
 *     @SWG\Response(
 *         response=404,
 *         description="Ressource introuvable"
 *     ),
 *     @SWG\Response(
 *          response="200", 
 *          description="Connexion avec le serveur avec succès")
 * )
 */
$app->put('/supplierorders', 'SupplierController@editOrder');


//doc route
$app->get(config('swagger-lume.routes.docs'), function ($page = 'api-docs.json') {
    $filePath = config('swagger-lume.paths.docs')."/{$page}";

    if (File::extension($filePath) === '') {
        $filePath .= '.json';
    }

    if (! File::exists($filePath)) {
        App::abort(404, "Cannot find {$filePath}");
    }

    $content = File::get($filePath);

    return new Response($content, 200, [
        'Content-Type' => 'application/json',
    ]);
});

//ROUTES FOR SUPPLIERS

/**
 * @SWG\Get(
 *     path="/supplier",
 *     tags={"Gestion des fournisseurs"},
 *     summary="Toutes les fournisseurs",
 *     description="Liste de toutes les fournisseurs",
 *     operationId="getAllSupplier",
 *     produces={"application/json"},
 *     @SWG\Response(
 *         response=500,
 *         description="Echec de la transaction"
 *     ),
 *     @SWG\Response(
 *         response=404,
 *         description="Page demander introuvable"
 *     ),
 *     @SWG\Response(
 *          response="200", 
 *          description="Connexion avec le serveur avec succès")
 * )
 */   
 $app->get('/supplier', 'SupplierController@getAllSupplier');


/**
 * @SWG\Get(
 *     path="/supplier/{id}",
 *     tags={"Gestion des fournisseurs"},
 *     summary="Une fournisseur",
 *     description="Description d'une fournisseur au moins une information passé en parametre",
 *     operationId="getOneSupplier",
 *     produces={"application/json"},
 *    @SWG\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID ou Key du fournisseur",
 *         required=true,
 *         type="string"
 *     ),
 *     @SWG\Response(
 *         response=500,
 *         description="Echec de la transaction"
 *     ),
 *     @SWG\Response(
 *         response=404,
 *         description="Page demander introuvable"
 *     ),
 *     @SWG\Response(
 *          response="200", 
 *          description="Connexion avec le serveur avec succès")
 * )
 */ 
$app->get('/supplier/{id}', 'SupplierController@getOneSupplier');


/**
 * @SWG\Post(
 *     path="/supplier",
 *     tags={"Gestion des fournisseurs"},
 *     summary="Création d'une fournisseur",
 *     description="Création d'une fournisseur au moyen des informations passées en paramètre",
 *     operationId="create",
 *     produces={"application/json"},
 *    @SWG\Parameter(
 *         name="name",
 *         in="query",
 *         description="Nom du fournisseur",
 *         required=true,
 *         type="string"
 *     ),
*    @SWG\Parameter(
 *         name="key",
 *         in="query",
 *         description="Key du fournisseur",
 *         required=true,
 *         type="string"
 *     ),
*     @SWG\Response(
 *         response=500,
 *         description="Echec de la transaction"
 *     ),
 *     @SWG\Response(
 *         response=404,
 *         description="Page demander introuvable"
 *     ),
 *     @SWG\Response(
 *          response="200", 
 *          description="Connexion avec le serveur avec succès")
 * )
 */ 
$app->post('/supplier', 'SupplierController@create');


/**
 * @SWG\Put(
 *     path="/supplier/{id}",
 *     tags={"Gestion des fournisseurs"},
 *     summary="Edition d'une fournisseur",
 *     description="Edition d'une fournisseur au moyen des informations passées en paramètre",
 *     operationId="update",
 *     produces={"application/json"},
 *     @SWG\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID du fournisseur",
 *         required=true,
 *         type="integer"
 *     ),
 *     @SWG\Parameter(
 *         name="name",
 *         in="query",
 *         description="Nom du fournisseur",
 *         required=true,
 *         type="string"
 *     ),
 *    @SWG\Parameter(
 *         name="key",
 *         in="query",
 *         description="Key du fournisseur",
 *         required=true,
 *         type="string"
 *     ),
 *     @SWG\Response(
 *         response=500,
 *         description="Echec de la transaction"
 *     ),
 *     @SWG\Response(
 *         response=404,
 *         description="Page demander introuvable"
 *     ),
 *     @SWG\Response(
 *          response="200", 
 *          description="Connexion avec le serveur avec succès")
 * )
 */ 
$app->put('/supplier/{id}', 'SupplierController@update');