// Simple parallax on scroll for elements with [data-parallax]
// Usage: add data-parallax and optional data-speed (default 0.2), data-rotate (deg), data-float (true)
(function(){
  const els = new Set();
  let ticking = false;

  function onScroll(){
    if(!ticking){
      window.requestAnimationFrame(apply);
      ticking = true;
    }
  }

  function apply(){
    const scrollY = window.scrollY || window.pageYOffset;
    els.forEach(el => {
      const rect = el.getBoundingClientRect();
      const offsetTop = rect.top + scrollY;
      const speed = parseFloat(el.getAttribute('data-speed') || '0.2');
      const rotate = parseFloat(el.getAttribute('data-rotate') || '0');
      const y = (scrollY - offsetTop + window.innerHeight * 0.3) * speed;
      let t = `translate3d(0, ${y.toFixed(1)}px, 0)`;
      if(rotate){ t += ` rotate(${(y*rotate*0.01).toFixed(2)}deg)`; }
      el.style.transform = t;
    });
    ticking = false;
  }

  function init(){
    document.querySelectorAll('[data-parallax]').forEach(el => els.add(el));
    if(els.size){
      window.addEventListener('scroll', onScroll, { passive: true });
      window.addEventListener('resize', onScroll);
      onScroll();
    }
  }

  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', init);
  } else { init(); }
})();
