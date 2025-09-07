(function(){
  function ready(fn){ if(document.readyState!='loading'){ fn(); } else { document.addEventListener('DOMContentLoaded', fn); } }
  function fmt(sec){ sec=Math.max(0,Math.floor(sec)); var m=Math.floor(sec/60), s=sec%60; return String(m).padStart(2,'0')+':'+String(s).padStart(2,'0'); }
  function toast(msg){
    var box = document.getElementById('rtp-toast'); if(!box) return;
    box.textContent = msg; box.style.display='block'; box.classList.add('show');
    setTimeout(function(){ box.classList.remove('show'); box.style.display='none'; }, 1400);
  }
  function esc(s){ return String(s).replace(/[&<>"']/g, function(m){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',\"'\":'&#039;'}[m]); }); }

  ready(function(){
    var root = document.querySelector('section.rtp-hero[data-rtp="1"]');
    if(!root){ return; }
    var players = []; // {name, dist, startDelaySec, rowEl}
    var nameInp  = document.getElementById('rtp-name');
    var distInp  = document.getElementById('rtp-distance');
    var durInp   = document.getElementById('rtp-duration-min');
    var startBtn = document.getElementById('rtp-start');
    var tbody    = document.querySelector('#rtp-table tbody');
    var nextName = document.getElementById('rtp-next-name');
    var nextIn   = document.getElementById('rtp-next-in');
    var arrival  = document.getElementById('rtp-arrival');
    var card     = document.getElementById('rtp-card');

    var timers = { arrival:null, next:null, per:{} };
    function clearTimers(){
      if(timers.arrival) clearInterval(timers.arrival);
      if(timers.next) clearInterval(timers.next);
      Object.keys(timers.per).forEach(function(k){ clearInterval(timers.per[k]); });
      timers.per = {};
    }
    function enableStart(){ startBtn.disabled = players.length===0; }
    function updateNextPreview(){
      var max = players.length ? players[0].dist : 0;
      var next = players.find(function(p){ return p.dist===max; });
      if(!next){ nextName.textContent='—'; nextIn.textContent='—'; return; }
      nextName.textContent = next.name;
      nextIn.textContent = 'first';
    }
    function render(){
      players.sort(function(a,b){ return b.dist - a.dist; });
      tbody.innerHTML='';
      var max = players.length ? players[0].dist : 0;
      players.forEach(function(p,i){
        p.startDelaySec = (max - p.dist);
        var tr=document.createElement('tr');
        tr.innerHTML = '<td class="rtp-rank">'+(i+1)+'</td>'+
                       '<td class="rtp-name">'+esc(p.name)+'</td>'+
                       '<td class="rtp-dist">'+p.dist+'</td>'+
                       '<td class="rtp-starts-in">--:--</td>'+
                       '<td class="rtp-status">Waiting</td>'+
                       '<td class="rtp-actions-td">'+
                         '<button type="button" class="rtp-icon-btn rtp-edit" data-name="'+esc(p.name)+'" aria-label="Edit">✏️</button> '+
                         '<button type="button" class="rtp-icon-btn rtp-remove" data-name="'+esc(p.name)+'" aria-label="Remove">🗑</button>'+
                       '</td>';
        p.rowEl = tr; tbody.appendChild(tr);
      });
      enableStart(); updateNextPreview();
    }

    // actions
    function addPlayer(){
      var name = (nameInp.value||'').trim();
      var dist = parseInt(distInp.value,10);
      if(!name || isNaN(dist) || dist<0){ toast('Enter valid name & distance'); return; }
      players.push({ name:name, dist:dist, startDelaySec:0, rowEl:null });
      nameInp.value=''; distInp.value='';
      render();
      toast('Player added');
    }
    function editPlayer(name){
      var current = players.find(function(pp){ return pp.name===name; });
      if(!current) return;
      var newName = prompt('Player name:', current.name);
      if(newName===null) return; newName=newName.trim(); if(!newName){ toast('Name empty'); return; }
      var newDistStr = prompt('Distance to castle (seconds):', String(current.dist));
      if(newDistStr===null) return;
      var newDist = parseInt(newDistStr,10); if(isNaN(newDist)||newDist<0){ toast('Enter valid distance'); return; }
      current.name=newName; current.dist=newDist; render(); toast('Updated');
    }
    function removePlayer(name){
      players = players.filter(function(pp){ return pp.name!==name; });
      render(); toast('Removed');
    }
    var startEpoch=0, arrivalEpoch=0, rallyDurSec=5*60;
    function start(){
      if(players.length===0){ toast('Add players first'); return; }
      clearTimers(); render();
      rallyDurSec = Math.max(60, parseInt(durInp.value,10)*60 || 300);
      var max = players[0].dist;
      startEpoch = Date.now();
      arrivalEpoch = startEpoch + (rallyDurSec + max)*1000;

      function upArrival(){
        var left = Math.max(0, Math.floor((arrivalEpoch - Date.now())/1000));
        arrival.textContent = fmt(left);
        if(left<=0) clearInterval(timers.arrival);
      }
      upArrival(); timers.arrival = setInterval(upArrival, 1000);

      players.forEach(function(p){
        var startAt = startEpoch + p.startDelaySec*1000;
        var startsEl = p.rowEl.querySelector('.rtp-starts-in');
        var statusEl = p.rowEl.querySelector('.rtp-status');
        p.rowEl.classList.remove('rtp-active','rtp-done');
        function upP(){
          var left = Math.floor((startAt - Date.now())/1000);
          if(left>0){
            startsEl.textContent = 'Starts in '+fmt(left);
            statusEl.textContent = 'Waiting';
          }else{
            startsEl.textContent = 'SEND NOW!';
            statusEl.textContent = 'Go';
            p.rowEl.classList.add('rtp-active');
            clearInterval(timers.per[p.name]);
          }
        }
        upP(); timers.per[p.name] = setInterval(upP, 500);
      });

      function upNext(){
        var next=null, best=Infinity;
        players.forEach(function(p){
          var left = (startEpoch + p.startDelaySec*1000 - Date.now())/1000;
          if(left>0 && left<best){ best=left; next=p; }
        });
        if(!next){ nextName.textContent='—'; nextIn.textContent='—'; return; }
        nextName.textContent = next.name;
        nextIn.textContent = 'in ' + fmt(best);
      }
      upNext(); timers.next = setInterval(upNext, 500);
      toast('Started');
    }
    function resetAll(){
      clearTimers();
      arrival.textContent='--:--'; nextName.textContent='—'; nextIn.textContent='—'; render();
      toast('Reset');
    }
    function clearAll(){
      clearTimers(); players=[]; nameInp.value=''; distInp.value='';
      arrival.textContent='--:--'; nextName.textContent='—'; nextIn.textContent='—'; render();
      toast('Cleared');
    }

    // event delegation (click + touchstart)
    function handleAction(el){
      if(!el) return;
      var act = el.getAttribute('data-action');
      if(act==='add') addPlayer();
      else if(act==='reset') resetAll();
      else if(act==='clear') clearAll();
      else if(act==='start') start();
      else if(el.classList.contains('rtp-edit')) editPlayer(el.getAttribute('data-name'));
      else if(el.classList.contains('rtp-remove')) removePlayer(el.getAttribute('data-name'));
    }
    card.addEventListener('click', function(e){ handleAction(e.target.closest('[data-action], .rtp-edit, .rtp-remove')); });
    card.addEventListener('touchstart', function(e){ var t=e.target.closest('[data-action], .rtp-edit, .rtp-remove'); if(t){ e.preventDefault(); handleAction(t);} }, {passive:false});

    // initial render
    render();
  });
})();