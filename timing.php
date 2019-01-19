				<?php
					$data = [
					  	'chat_id' => $userId,
					  	'text' => date("H:m:s"),
					];
					$response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
				?>