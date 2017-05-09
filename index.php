<?php
/**
 * Secret santa Generator
 */

$participants_file = dirname( __FILE__ ) . '/participants.txt';
$secret_santa_file = dirname( __FILE__ ) . '/secret-santa.txt';

$participants_handler = fopen( $participants_file, 'r' );

if ( ! $participants_handler ) {
	die(
		"There's nothing to be done.\n\nProvide a list of participants in the
form of a file named: participants.txt\nThe file should have one email per line."
	);
}

$santas_handler = fopen( $secret_santa_file, 'x' );

if ( ! $santas_handler ) {
	die( 'The santa callings were already sent. Happy shopping.' );
}

$participants = fread( $participants_handler, filesize( $participants_file ) );

if ( empty( $participants ) ) {
	die( 'There are no participants :(' );
}

$participants = explode( "\n", $participants );
$number_participants = count( $participants );

// Trim the empty spaces (because people and editors are stupid).
foreach ( $participants as $key => $participant ) {
	if ( empty( $participant ) ) {
		unset( $participants[ $key ] );
	} else {
		$participant = explode( ',', $participant );
		$participants[ $key ] = array(
			'name'  => trim( $participant[0] ),
			'email' => trim( $participant[1] ),
		);
	}
}
// Let's mix things.
shuffle( $participants );

var_dump($participants);
die();


for ( $i = 0; $i < $number_participants; $i++ ) {
	if ( $i !== $number_participants - 1 ) {
		$santa = $participants[ $i ];
		$kid = $participants[ $i + 1 ];
	} else {
		$santa = $participants[ $i ];
		$kid = $participants[0];
	}
	$pair = "{$santa['name']} ({$santa['email']}) -> {$kid['name']} ({$kid['email']})\n";
	fwrite( $santas_handler, $pair );

	// Send the email to the santa.
	$to      = $santa['email'];
	$subject = 'Secret Santa da Comemoração de Natal/Solstício de Inverno';
	$message = "Olá querido Pai Natal,\n\nA tua criança este ano é: {$kid['name']}\n
(O email dela, caso seja necessário, é: {$kid['email']})\n";

	$mail = mail( $to, $subject, $message );

	if ( $mail ) {
		printf( '%d santas in the house...', $i + 1 );
	} else {
		die( 'No email' );
	}
}

// Close files.
fclose( $participants_handler );
fclose( $santas_handler );
