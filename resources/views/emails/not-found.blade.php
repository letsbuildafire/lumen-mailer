<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>404 - Email Template Not Found</title>
		<style>
			body,
			.container {
				background-color: #eee;
				height: 100vh;
				margin: 0;
				padding: 0;
				position: relative;
				width: 100%;
			}
			.container:before {
				content: '';
				display: inline-block;
				font-size: 0;
				height: 100vh;
				margin-left: -1rem;
				vertical-align: middle;
				width: 0;
			}
			.error-message {
				color: #888;
				display: inline-block;
				font-family: 'Helvetica Neue', Helvetica, Arial;
				font-size: 1.6rem;
				text-align: center;
				text-transform: uppercase;
				vertical-align: middle;
				width: 100%;
				text-indent: 1rem;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<div class="error-message">
				<h2>Template Not Found</h2>
			</div>
		</div>
	</body>
</html>
