<!DOCTYPE html>
<html>
<head>
    <title>Monitoring IoT</title>
</head>
<body>

<h1>Monitoring Pintu</h1>

<h2>Jarak:</h2>
<h1 id="angka-jarak">0 cm</h1>

<h2>Status:</h2>
<h1 id="teks-status">-</h1>

<button onclick="toggleBuzzerWeb('ON')">Nyalakan Buzzer</button>
<button onclick="toggleBuzzerWeb('OFF')">Matikan Buzzer</button>

<script type="module">
  import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.1/firebase-app.js";
  import { getDatabase, ref, onValue, set } from "https://www.gstatic.com/firebasejs/10.8.1/firebase-database.js";

  // 🔥 CONFIG SUDAH DIGANTI
  const firebaseConfig = {
    apiKey: "AIzaSyBCpmeW6CkGOhhpOlKxBYqpkX_d4m45sFc",
    databaseURL: "https://coba-b57da-default-rtdb.asia-southeast1.firebasedatabase.app",
    projectId: "coba-b57da"
  };

  const app = initializeApp(firebaseConfig);
  const db = getDatabase(app);

  // REALTIME DATA
  const jarakRef = ref(db, 'monitoring/jarak');
  const statusRef = ref(db, 'monitoring/status');

  onValue(jarakRef, (snapshot) => {
    const jarak = snapshot.val();
    document.getElementById('angka-jarak').innerText = jarak + " cm";
  });

  onValue(statusRef, (snapshot) => {
    const status = snapshot.val();
    document.getElementById('teks-status').innerText = status;
  });

  // CONTROL BUZZER
  window.toggleBuzzerWeb = function(state) {
    set(ref(db, 'monitoring/buzzer_state'), state)
      .then(() => {
        alert("Buzzer: " + state);
      })
      .catch((error) => {
        console.error(error);
      });
  }
</script>

</body>
</html>