
<?php

AD::route('GET','/personal/{user_id}/{fam}', function($request, $response) {
	$response->json($request)->render();
});