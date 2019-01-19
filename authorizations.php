<?php
//$conn = new mysqli("db4free.net", "trial4life", "16021993", "tradepkmn");
	$query = "SELECT * FROM `auth_groups`";
	$result = mysqli_query($conn,$query);
	$authorizedChats = $authorizedChatsNames = array();
	while ($row = mysqli_fetch_assoc($result)) {
		array_push($authorizedChats, intval($row['groupID']));
		array_push($authorizedChatsNames, $row['groupName']);
	}

	$query = "SELECT * FROM `admins`";
	$result = mysqli_query($conn,$query);
	$admins = array();
	while ($row = mysqli_fetch_assoc($result)) {
		array_push($admins, $row['username']);
	}

/*
	$bot_Exeggutor = 158754689;
	$group_PogoTube42 = -1001204753064;
	$group_NordEstLegit = -1001119443518;
	$group_admin = -1001205498567;
	$group_SalarioParioli = -1001369640732;

	$authorizedChats = array(
		$group_PogoTube42,
		$group_NordEstLegit,
		//$bot_Exeggutor,
		$group_admin,
		$group_SalarioParioli,
	);

	$authorizedChatsNames = array(
		'PoGoRaidRomaNordEst',
		' w',
	);
*/
/*
	$admins = array(
		'Trial4life',
		'DadyGC',
		'medix93',
		'Barrazar',
		'Giulia_Valorosi',
		'MenoMenotti',
		'Illidanrex',
		'ProtusPrime',
		'HarlockHrk',
	);
*/

	$bannedUsers = array(

	);
//$conn->close();
?>