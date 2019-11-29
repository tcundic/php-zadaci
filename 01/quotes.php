<?php

if (isset($_POST['action']) && function_exists($_POST['action'])) {
	$action = $_POST['action'];
	echo json_encode($action());
}

function getRandomQuote() {
	$quotes = getAllQuotes();

	$id = array_rand($quotes);
	$quote = $quotes[$id];

	return $quote;
}


/*
* Function returns list of available quotes
* @returns array
**/
function getAllQuotes() {
	return [
		'1' => [	
			'quote' => 'Monkeys are superior to men in this: when a monkey looks into a mirror, he sees a monkey.',
			'author' => 'Malcolm de Chazal'
		],
		'2' => [
			'quote' => 'They couldn\'t hit an elephant at this dist...',
			'author' => 'Gen. John Sedgwick'
		],
		'3' => [
			'quote' => 'Electronics need smoke to work. Proof: once the smoke comes out of them, they stop working.',
			'author' => 'Anonymous'
		],
		'4' => [
			'quote' => 'Giving up smoking is the easiest thing in the world. I know because I\'ve done it thousands of times.',
			'author' => 'Mark Twain'
		],
		'66' => [
			'quote' => 'I do not know with what weapons World War III will be fought, but World War IV will be fought with sticks and stones.',
			'author' => 'Albert Einstein'
		],
		'42' => [
			'quote' => 'Flying is learning how to throw yourself at the ground and miss.',
			'author' => 'Douglas Adams'
		],
		'8' => [
			'quote' => 'Do not look into laser beam with remaining eye.',
			'author' => 'Anonymous'
		],
		'6' => [
			'quote' => 'Ni jedno ljudsko biće ne može opstati samo, bez zajednice.',
			'author' => 'Dalai Lama'
		],
		'7' => [
			'quote' => 'Bolje živjeti 100 godina kao milijunaš, nego 7 dana u bijedi.',
			'author' => 'Alan Ford'
		],
		'5' => [
			'quote' => "- Have you ever heard of the Emancipation Proclamation?\n- I dont listen to hip-hop.",
			'author' => 'Chef vs General, South Park'
		],		
	];
}