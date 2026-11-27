const filters = document.querySelector('#player-filters');
const cards = document.querySelector('#player-list');
const status = document.querySelector('#player-status');
const more = document.querySelector('#load-more');
const panel = document.querySelector('#profile-panel');
const selection = document.querySelector('#selected-profile');
const contactButton = document.querySelector('#contact-player');
const actionStatus = document.querySelector('#action-status');
const reportDialog = document.querySelector('#report-dialog');
const reportForm = document.querySelector('#report-form');
const reportStatus = document.querySelector('#report-status');
const csrf = document.querySelector('meta[name=csrf-token]').content;
const compact = window.matchMedia('(max-width: 1100px)');
let players = [];
let selected;
let opener;
let searchRequest;
let nextPage;
let timer;

function el(tag, value, className) {
  const node = document.createElement(tag);
  if (className) node.className = className;
  if (value !== undefined) node.textContent = value;
  return node;
}

function initials(name) {
  return [...name.trim()].slice(0, 1).join('').toLocaleUpperCase('fr') || '?';
}

function tab(name) {
  for (const button of panel.querySelectorAll('[data-tab]')) button.setAttribute('aria-pressed', String(button.dataset.tab === name));
  for (const pane of panel.querySelectorAll('[data-pane]')) pane.hidden = pane.dataset.pane !== name;
}

function listItems(id, values, fallback) {
  document.querySelector(id).replaceChildren(...(values.length ? values : [fallback]).map(value => el('li', value)));
}

function closeProfile() {
  panel.classList.remove('open');
  if (compact.matches) opener?.focus();
}

function selectPlayer(player, button) {
  selected = player;
  if (button) opener = button;
  for (const control of cards.querySelectorAll('[data-player]')) {
    control.setAttribute('aria-pressed', String(Number(control.dataset.player) === player.id));
  }
  document.querySelector('#no-selection').hidden = true;
  selection.hidden = false;
  document.querySelector('#selected-avatar').textContent = initials(player.name);
  document.querySelector('#selected-name').textContent = player.name;
  document.querySelector('#score-value').textContent = player.score + ' %';
  document.querySelector('#selected-score').value = player.score;
  const info = [['Plateforme', player.platform], ['Niveau', player.level], ['Style', player.play_style], ['Langue', player.language]];
  document.querySelector('#selected-info').replaceChildren(...info.flatMap(([key, value]) => [
    el('dt', key), el('dd', value || 'À préciser'),
  ]));
  document.querySelector('#selected-bio').textContent = player.bio || 'Aucune biographie renseignée.';
  listItems('#selected-games', player.games, 'Aucun jeu renseigné');
  listItems('#selected-slots', player.availability_slots, 'Aucun créneau renseigné');
  document.querySelector('#selected-availability').textContent = player.availability || '';
  contactButton.disabled = Boolean(player.contact_status);
  contactButton.textContent = { pending: 'Demande en cours', accepted: 'Contact accepté', declined: 'Demande refusée' }[player.contact_status] || 'Demander à jouer';
  actionStatus.textContent = '';
  tab('about');
  if (button && compact.matches) {
    panel.classList.add('open');
    panel.focus();
  }
}

function render() {
  cards.replaceChildren();
  for (const player of players) {
    const row = el('article', undefined, 'player');
    const main = el('div', undefined, 'player-data');
    const meta = [player.games[0], player.platform, player.level, player.play_style].filter(Boolean).join(' · ');
    main.append(el('h3', player.name), el('p', meta), el('p', player.availability_slots.join(' · ') || player.availability || 'Disponibilités à préciser'));
    const button = el('button', 'Voir le profil', 'button');
    button.type = 'button';
    button.dataset.player = player.id;
    button.setAttribute('aria-label', 'Consulter le profil de ' + player.name);
    button.setAttribute('aria-pressed', String(selected?.id === player.id));
    button.addEventListener('click', () => selectPlayer(player, button));
    row.append(el('span', initials(player.name), 'avatar'), main, el('span', player.score + ' %', 'chip'), button);
    cards.append(row);
  }
}

async function search(page = 1) {
  searchRequest?.abort();
  const controller = new AbortController();
  searchRequest = controller;
  status.textContent = 'Recherche en cours…';
  more.disabled = true;
  const params = new URLSearchParams(new FormData(filters));
  params.set('page', page);
  try {
    const response = await fetch(filters.dataset.endpoint + '?' + params, { signal: controller.signal, headers: { Accept: 'application/json' } });
    if (response.status === 401) throw new Error('Ta session a expiré. Recharge la page pour te connecter.');
    if (!response.ok) throw new Error('La recherche est indisponible. Réessaie.');
    const data = await response.json();
    players = page === 1 ? data.players : [...players, ...data.players];
    nextPage = data.next_page;
    const current = players.find(player => player.id === selected?.id);
    if (!current && page === 1) {
      selected = undefined;
      selection.hidden = true;
      document.querySelector('#no-selection').hidden = false;
      panel.classList.remove('open');
    }
    render();
    if (current && !panel.classList.contains('open')) selectPlayer(current);
    status.textContent = data.total ? data.total + (data.total === 1 ? ' joueur trouvé' : ' joueurs trouvés') : 'Aucun joueur pour ces critères.';
    more.hidden = !nextPage;
  } catch (error) {
    if (error.name !== 'AbortError') status.textContent = error.message;
  } finally {
    if (searchRequest === controller) more.disabled = false;
  }
}

function updateGames() {
  const game = filters.elements.namedItem('game').value;
  for (const button of document.querySelectorAll('[data-game]')) button.setAttribute('aria-pressed', String(button.dataset.game === game));
}

async function submitAction(url, payload) {
  const response = await fetch(url, {
    method: 'POST',
    headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
    body: JSON.stringify(payload),
  });
  const body = await response.json().catch(() => ({}));
  if (!response.ok) {
    if (response.status === 419 || response.status === 401) throw new Error('Ta session a expiré. Recharge la page.');
    if (response.status === 429) throw new Error('Tu as atteint la limite de tentatives. Réessaie plus tard.');
    throw new Error(body.message || 'Action indisponible.');
  }
  return body;
}

for (const button of document.querySelectorAll('[data-game]')) {
  button.addEventListener('click', () => {
    filters.elements.namedItem('game').value = button.dataset.game;
    updateGames();
    search();
  });
}
for (const image of document.querySelectorAll('.game-cover img')) {
  const hideImage = () => { image.hidden = true; };
  image.addEventListener('error', hideImage);
  if (image.complete && !image.naturalWidth) hideImage();
}
filters.addEventListener('submit', event => { event.preventDefault(); clearTimeout(timer); search(); });
filters.elements.namedItem('search').addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(search, 250); });
filters.addEventListener('change', event => { if (event.target.tagName === 'SELECT') { updateGames(); search(); } });
filters.addEventListener('reset', () => { clearTimeout(timer); setTimeout(() => { updateGames(); search(); }, 0); });
more.addEventListener('click', () => { if (nextPage) search(nextPage); });
document.querySelector('#show-filters').addEventListener('click', event => {
  const advanced = document.querySelector('#advanced');
  advanced.hidden = !advanced.hidden;
  event.currentTarget.setAttribute('aria-expanded', String(!advanced.hidden));
});
for (const button of panel.querySelectorAll('[data-tab]')) button.addEventListener('click', () => tab(button.dataset.tab));
document.querySelector('#close-profile').addEventListener('click', closeProfile);
document.addEventListener('keydown', event => { if (event.key === 'Escape' && !reportDialog.open && panel.classList.contains('open')) closeProfile(); });
document.querySelector('#report-player').addEventListener('click', () => {
  reportForm.reset();
  reportStatus.textContent = '';
  reportDialog.showModal();
});
document.querySelector('#cancel-report').addEventListener('click', () => reportDialog.close());
reportForm.addEventListener('submit', async event => {
  event.preventDefault();
  const button = reportForm.querySelector('button[type=submit]');
  const target = selected;
  button.disabled = true;
  try {
    const result = await submitAction(target.report_url, Object.fromEntries(new FormData(reportForm)));
    reportStatus.textContent = result.message;
    button.hidden = true;
  } catch (error) { reportStatus.textContent = error.message; }
  finally { button.disabled = false; }
});
reportDialog.addEventListener('close', () => { reportForm.querySelector('button[type=submit]').hidden = false; });
contactButton.addEventListener('click', async () => {
  const target = selected;
  contactButton.disabled = true;
  try {
    const body = await submitAction(target.contact_url, {});
    target.contact_status = body.status;
    const contacts = document.querySelector('#contacts');
    contacts.querySelector('.hint')?.remove();
    const row = el('article', undefined, 'activity-row');
    row.append(el('strong', target.name), el('span', 'Demande envoyée'));
    contacts.querySelector('h2').after(row);
    if (selected?.id === target.id) {
      contactButton.textContent = 'Demande en cours';
      actionStatus.textContent = body.message;
    }
  } catch (error) {
    if (selected?.id === target.id) {
      actionStatus.textContent = error.message;
      contactButton.disabled = false;
    }
  }
});
function currentNav() {
  const hash = location.hash || '#accueil';
  for (const link of document.querySelectorAll('.rail a[href^="#"]')) {
    if (link.hash === hash) link.setAttribute('aria-current', 'location');
    else link.removeAttribute('aria-current');
  }
}
window.addEventListener('hashchange', currentNav);
currentNav();
search();
