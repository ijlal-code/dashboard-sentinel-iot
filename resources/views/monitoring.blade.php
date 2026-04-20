<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Sentinel IoT</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-900 text-white flex flex-col min-h-screen">

<!-- NAVBAR -->
<nav class="bg-slate-800 p-4 text-center text-xl font-bold text-cyan-400">
Sentinel
</nav>

<!-- CONTENT -->
<main class="flex-grow p-6">

<!-- REALTIME -->
<div class="grid md:grid-cols-2 gap-6">
<div class="bg-slate-800 p-6 rounded-xl">
<h2>Jarak</h2>
<h1 id="jarak" class="text-4xl text-cyan-400">0 cm</h1>
</div>

<div class="bg-slate-800 p-6 rounded-xl">
<h2>Status</h2>
<h1 id="status" class="text-4xl">-</h1>
</div>
</div>

<!-- LCD CONTROL -->
<div class="mt-6 bg-slate-800 p-6 rounded-xl">
<h2 class="text-cyan-400 mb-2">Kontrol LCD (Max 32 Karakter)</h2>

<input id="textNear" maxlength="32" placeholder="Teks < 50 cm"
class="text-black p-2 rounded w-full mb-2">

<input id="textFar" maxlength="32" placeholder="Teks >= 50 cm"
class="text-black p-2 rounded w-full mb-2">

<p id="errorText" class="text-red-400 hidden">Teks melebihi batas 32 karakter!</p>

<button onclick="setText()" class="bg-cyan-500 px-4 py-2 rounded mt-2">
Simpan
</button>
</div>

<!-- BUZZER -->
<div class="mt-6 bg-slate-800 p-6 rounded-xl">
<h2 class="text-red-400 mb-3">Kontrol Buzzer</h2>

<button onclick="buzzer('ON')" class="bg-green-500 px-4 py-2 rounded">ON</button>
<button onclick="buzzer('OFF')" class="bg-red-500 px-4 py-2 rounded ml-2">OFF</button>
</div>

<!-- FILTER -->
<div class="mt-6 bg-slate-800 p-6 rounded-xl">
<input type="date" id="filterDate" class="text-black p-2 rounded">
<select id="filterStatus" class="text-black p-2 rounded">
<option value="">Semua</option>
<option>ADA ORANG</option>
<option>AMAN</option>
</select>

<button onclick="renderTable()" class="bg-cyan-500 px-4 py-2 rounded">
Filter
</button>
</div>

<!-- TABLE -->
<div class="mt-6 bg-slate-800 p-6 rounded-xl overflow-auto">
<table class="w-full">
<thead>
<tr>
<th>Jarak</th>
<th>Status</th>
<th>Waktu</th>
</tr>
</thead>
<tbody id="tableData"></tbody>
</table>
</div>

</main>

<!-- FOOTER -->
<footer class="bg-slate-800 p-4 text-center text-cyan-400">
Sentinel
</footer>

<!-- 🔥 TOAST -->
<div id="toast"
class="fixed top-5 right-5 opacity-0 pointer-events-none transition-all duration-300">
</div>

<script type="module">
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.1/firebase-app.js";
import { getDatabase, ref, onValue, set } from "https://www.gstatic.com/firebasejs/10.8.1/firebase-database.js";

const app = initializeApp({
apiKey: "AIzaSy...",
databaseURL: "https://coba-b57da-default-rtdb.asia-southeast1.firebasedatabase.app"
});

const db = getDatabase(app);

let historyData = [];
let toastTimeout;

// 🔥 TOAST FUNCTION
function showToast(msg, color="bg-green-500") {
const toast = document.getElementById("toast");

// isi toast
toast.innerHTML = `
<div class="${color} text-white px-4 py-3 rounded shadow-lg flex items-center gap-4">
  <span>${msg}</span>
  <button onclick="closeToast()" class="font-bold text-lg">&times;</button>
</div>
`;

// tampilkan
toast.classList.remove("opacity-0","pointer-events-none");
toast.classList.add("opacity-100");

// auto hide 3 detik
clearTimeout(toastTimeout);
toastTimeout = setTimeout(()=>{
  closeToast();
},3000);
}

// 🔥 CLOSE TOAST
window.closeToast = ()=>{
const toast = document.getElementById("toast");
toast.classList.remove("opacity-100");
toast.classList.add("opacity-0","pointer-events-none");
}

// REALTIME
onValue(ref(db,"monitoring/realtime"), snap=>{
let d = snap.val();
document.getElementById("jarak").innerText = d.jarak + " cm";
document.getElementById("status").innerText = d.status;
});

// HISTORY
onValue(ref(db,"monitoring/history"), snap=>{
let data = snap.val();
historyData = [];

for(let key in data){
historyData.push(data[key]);
}

historyData = historyData.slice(-30).reverse();
renderTable();
});

// FILTER
window.renderTable = ()=>{
let html = "";

const filterDate = document.getElementById("filterDate").value;
const filterStatus = document.getElementById("filterStatus").value;

historyData.forEach(d=>{
if(filterDate && !d.waktu.startsWith(filterDate)) return;
if(filterStatus && d.status !== filterStatus) return;

html += `
<tr>
<td>${d.jarak}</td>
<td>${d.status}</td>
<td>${d.waktu}</td>
</tr>
`;
});

document.getElementById("tableData").innerHTML = html;
}

// SET TEXT
window.setText = ()=>{
let near = document.getElementById("textNear").value;
let far = document.getElementById("textFar").value;

if(near.length > 32 || far.length > 32){
document.getElementById("errorText").classList.remove("hidden");
showToast("Teks terlalu panjang!", "bg-red-500");
return;
}

document.getElementById("errorText").classList.add("hidden");

set(ref(db,"monitoring/text_near"), near);
set(ref(db,"monitoring/text_far"), far);

showToast("Teks LCD berhasil disimpan");
}

// BUZZER
window.buzzer = (s)=>{
set(ref(db,"monitoring/buzzer_state"), s);
showToast("Buzzer " + s);
}

</script>

</body>
</html>