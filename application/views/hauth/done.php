<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	</head>
<body>
<p>ID: <?php echo $user_profile->identifier ?></p>
<p>Display Name: <?php echo $user_profile->displayName ?></p>
<p>First Name: <?php echo $user_profile->firstName ?></p>
<p>Last Name: <?php echo $user_profile->lastName ?></p>
<p>Nickname: <?php echo $user_profile->nickname ?></p>
<p>Gender: <?php echo $user_profile->gender ?></p>
<p>Birthdate: <?php echo $user_profile->birthdate ?></p>
<p>Email: <?php echo $user_profile->email ?></p>
<p>Email Verified: <?php echo $user_profile->emailVerified ?></p>
<p>Phone Number: <?php echo $user_profile->phoneNumber ?></p>
<p>Address: <?php echo $user_profile->address ?></p>
<p>Country: <?php echo $user_profile->country ?></p>
<p>Region: <?php echo $user_profile->region ?></p>
<p>City: <?php echo $user_profile->city ?></p>
<p>Zip/postcode: <?php echo $user_profile->zip ?></p>

<a href="http://local.codeigniter.co.uk/index.php/hauth/logout/PixelPin">LogOut</a>

</body>
</html>
