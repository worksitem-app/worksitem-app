<script>
(function(){
if(window.__kaintaLinkerLoaded)return; window.__kaintaLinkerLoaded=true;
const URL='https://kainta.com', RX=/Kainta İnşaat/g;
function convert(t){
 if(!t.nodeValue||!RX.test(t.nodeValue))return; RX.lastIndex=0;
 const p=t.parentElement;if(!p||p.closest('script,style,[data-kainta-auto-link]'))return;
 if(p.closest('a[href*="kainta.com"]'))return;
 const f=document.createDocumentFragment();let last=0,m,s=t.nodeValue;
 while((m=RX.exec(s))){f.append(s.slice(last,m.index));let e;
  if(p.closest('a,button')){e=document.createElement('span');e.onclick=x=>{x.preventDefault();x.stopPropagation();window.open(URL,'_blank','noopener')};}
  else{e=document.createElement('a');e.href=URL;e.target='_blank';e.rel='noopener';}
  e.dataset.kaintaAutoLink='1';e.textContent=m[0];e.style.textDecoration='underline';e.style.textUnderlineOffset='3px';e.style.cursor='pointer';f.append(e);last=RX.lastIndex;
 } f.append(s.slice(last));t.replaceWith(f);
}
function scan(r){const w=document.createTreeWalker(r,NodeFilter.SHOW_TEXT);let a=[];while(w.nextNode())a.push(w.currentNode);a.forEach(convert);}
document.addEventListener('DOMContentLoaded',()=>{scan(document.body);new MutationObserver(ms=>ms.forEach(m=>m.addedNodes.forEach(n=>{if(n.nodeType===3)convert(n);else if(n.nodeType===1)scan(n)}))).observe(document.body,{childList:true,subtree:true});});
})();
</script>
