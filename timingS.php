				<?php
					$data = [
					  	'chat_id' => $userId,
					  	'text' => 'Start: '.date("H:i:s"),
					];
					$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
				?>