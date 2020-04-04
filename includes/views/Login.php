<!DOCTYPE html>
<html>
<head>
	<title>Qualesk</title>
	<link rel="stylesheet" type="text/css" href="../static/css/main.css">
	<link rel="stylesheet" type="text/css" href="../static/css/view/login.css">
	<link rel="icon" type="image/x-icon" href="../static/img/favicon.png" />
	<script src="../static/js/jquery-3.4.0.min.js"></script>
	<script>
		function login() {
			$.ajax({
				method: "POST",
				url: "includes/services/userLogin.php",
				data: { 
					username: $( "input[name='username']" ).val(),
					password: $( "input[name='password']" ).val()
				}
			})
			.done(function( msg ) {
				if ( msg == true ) {
					location.reload();
				} else {
					$( "#text_nologin" ).css( 'display', 'inline' );
				}
			});
		}
	</script>
	<script>
		var TxtType = function(el, toRotate, period) {
			this.toRotate = toRotate;
			this.el = el;
			this.loopNum = 0;
			this.period = parseInt(period, 10) || 2000;
			this.txt = '';
			this.tick();
			this.isDeleting = false;
		};

		TxtType.prototype.tick = function() {
			var i = this.loopNum % this.toRotate.length;
			var fullTxt = this.toRotate[i];

			if (this.isDeleting) {
				this.txt = fullTxt.substring(0, this.txt.length - 1);
			} else {
				this.txt = fullTxt.substring(0, this.txt.length + 1);
			}

			this.el.innerHTML = '<span class="wrap">'+this.txt+'</span>';

			var that = this;
			var delta = 200 - Math.random() * 100;

			if (this.isDeleting) { delta /= 2; }

			if (!this.isDeleting && this.txt === fullTxt) {
				delta = this.period;
				this.isDeleting = true;
			} else if (this.isDeleting && this.txt === '') {
				this.isDeleting = false;
				this.loopNum++;
				delta = 500;
			}

			setTimeout(function() {
				that.tick();
			}, delta);
		};

		window.onload = function() {
			var elements = document.getElementsByClassName('typewrite');
			for (var i=0; i<elements.length; i++) {
				var toRotate = elements[i].getAttribute('data-type');
				var period = elements[i].getAttribute('data-period');
				if (toRotate) {
					new TxtType(elements[i], JSON.parse(toRotate), period);
				}
			}

			var css = document.createElement("style");
			css.type = "text/css";
			css.innerHTML = ".typewrite > .wrap { border-right: 0.08em solid #fff}";
			document.body.appendChild(css);
		};
	</script>
	<style>
		.inline p, input {
			display: inline;
		}

		.inline p:not(:first-child), input {
			margin-left: 12px;
		}

		.grid-container {
			padding: 0px;
			grid-gap: 0px;
		}

		.no-border {
			border-radius: 0px;
		}

		.bg1::before {
			content: "";
			position: absolute;
			top: 0; left: 0;
			width: 100%; height: 100%;
			background: url('../static/img/projback.jpg');
			filter: opacity(15%);
		}

		.bg1 {
			position: relative;
			height: calc(100vh - 153px);
			margin-bottom: 10px;
			background-color: transparent;
			filter: opacity(1);
		}

		.bg1 img {
			display: inline;
			margin-right: 32px;
		}

		.body-info {
			position: absolute;
			left: 50%;
			top: 50%;
			transform: translate(-50%, -50%);
		}

		.body-info p {
			display: inline;
			font-size: 64px;
			color: white;
			text-shadow: #1976D2 1.5px 1px;
		}

		input[type=text], input[type=password] {
			border: 1px solid rgba(128, 128, 128, 0.5);
			padding: 4px;
		}
	</style>
</head>
<body>
	<div class="grid-container">
		<? include 'elements/topBar.php'; ?>

		<div class="grid-item grid-item-3x1 no-border inline"><div>
			<p>Username</p><input type="text" name="username">
			<p>Password</p><input type="password" name="password">
			<input onclick="login()" type="button" value="Login">
			<p id="text_nologin">Incorrect credentials</p>
		</div></div>

		<div class="grid-item grid-item-3x1 no-border bg1"><div>
			<div class="body-info">
				<p class="typewrite" data-period="1500" data-type='[ "Data analytics made simple.", "Real-time.", "Extensible.", "Modular." ]'>
					<span class="wrap"></span>
				</p>
			</div>
		</div></div>

		<? include 'elements/bottomBar.php'; ?>
	</div>
</body>
</html>