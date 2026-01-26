document.addEventListener('DOMContentLoaded', () => {

  const scoreColors = {
    1:'#dc2626',2:'#ef4444',3:'#f87171',4:'#fb7185',
    5:'#facc15',6:'#fde047',7:'#fbbf24',
    8:'#86efac',9:'#4ade80',10:'#16a34a'
  };

  const form = document.getElementById('feedbackForm');
  const submitBtn = document.getElementById('submitBtn');

  // =====================
  // SCALE BUTTONS
  // =====================
  document.querySelectorAll('.scale').forEach(scale => {
    const name  = scale.dataset.name;
    const input = scale.parentElement.querySelector(`input[name="${name}"]`);
    const error = scale.parentElement.querySelector('.error');
    const emoji = scale.parentElement.querySelector('.emoji');
    const label = scale.parentElement.querySelector('.emoji-label');
    const superText = scale.parentElement.querySelector('.super-text');


    for (let i = 1; i <= 10; i++) {
      const btn = document.createElement('span');
      btn.textContent = i;

      btn.addEventListener('click', () => {

        // reset scale
        scale.querySelectorAll('span').forEach(s => {
          s.classList.remove('active');
          s.style.background = 'var(--ihg-glass)';
          s.style.removeProperty('--score-color');
        });

        // activate
        const color = scoreColors[i];
        btn.classList.add('active');
        btn.style.background = color;
        btn.style.setProperty('--score-color', color);
        input.value = i;

        // emoji
        emoji.className = 'emoji';
        superText.classList.remove('show');

        if (i <= 4) {
          emoji.textContent = '😑';
          emoji.classList.add('emoji-bad');
          label.textContent = 'Bad';

        } else if (i <= 7) {
          emoji.textContent = i === 7 ? '🙂' : '😐';
          emoji.classList.add('emoji-average');
          label.textContent = 'Average';

        } else if (i <= 9) {
          emoji.textContent = i === 8 ? '😊' : '😄';
          emoji.classList.add('emoji-good');
          label.textContent = 'Great';

        } else {
          emoji.textContent = '😍';
          emoji.classList.add('emoji-good');
          label.textContent = 'Great';
          superText.classList.add('show'); // 🔥 INI KUNCI
        }
        
        error.style.display = 'none';
        scale.parentElement.classList.remove('shake');

        updateProgress();
      });

      scale.appendChild(btn);
    }
  });
  

  // =====================
  // SUBMIT VALIDATION
  // =====================
  form.addEventListener('submit', e => {
  e.preventDefault();

  let valid = true;

  document.querySelectorAll('.question').forEach(q => {
    const input = q.querySelector('input[type=hidden]');
    const error = q.querySelector('.error');

    if (!input.value) {
      valid = false;
      error.style.display = 'block';

      q.classList.remove('shake');
      void q.offsetWidth;
      q.classList.add('shake');
    }
  });

  if (!valid) return;

  const btn = document.getElementById('submitBtn');
  btn.classList.add('loading');

  // 💥 CONFETTI
  launchConfettiFromButton(btn);

  // ⏳ REDIRECT AFTER TRANSITION
  setTimeout(() => {
    form.submit();
  }, 1000); // 🔥 timing pas
});


  // =====================
  // PROGRESS BAR
  // =====================
  function updateProgress(){
    const total = document.querySelectorAll('.question').length;
    let filled = 0;

    document.querySelectorAll('.question input[type=hidden]').forEach(i=>{
      if(i.value) filled++;
    });

    document.getElementById('progressBar').style.width =
      Math.round((filled / total) * 100) + '%';
  }

});

// =====================
// CONFETTI GOD MODE
// =====================
function launchConfettiFromButton(btn){

  const rect = btn.getBoundingClientRect();
  const x = (rect.left + rect.width / 2) / window.innerWidth;
  const y = rect.top / window.innerHeight;

  // 🔊 sound
  const pop = document.getElementById('popSound');
  if (pop) {
    pop.currentTime = 0;
    pop.play();
  }

  // 💥 blast 1
  confetti({
    particleCount: 180,
    spread: 100,
    startVelocity: 55,
    gravity: 1,
    origin: { x, y }
  });

  // 💥 blast 2
  confetti({
    particleCount: 120,
    spread: 140,
    startVelocity: 40,
    decay: 0.92,
    origin: { x, y }
  });
}
