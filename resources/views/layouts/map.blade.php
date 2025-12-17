{{-- === FLOATING ACTION BUTTON (FAB) === --}}
<button class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center"
        style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; z-index: 1050; transition: transform 0.2s;"
        data-bs-toggle="modal"
        data-bs-target="#graphModal"
        onmouseover="this.style.transform='scale(1.1)'"
        onmouseout="this.style.transform='scale(1)'"
        title="Vue de la Table">
    <i class="bi bi-diagram-3-fill fs-3"></i>
</button>

{{-- === MODAL GRAPHE === --}}
<div class="modal fade" id="graphModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-fullscreen-md-down">
        <div class="modal-content bg-dark text-white border-0 shadow-lg" style="height: 85vh;">

            <div class="modal-header border-bottom border-secondary bg-black bg-opacity-25">
                <h5 class="modal-title text-uppercase fw-bold ls-1">
                    <i class="bi bi-people-fill me-2 text-primary"></i>Table de Jeu
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-0 position-relative overflow-hidden" id="map-viewport" style="background-color: #212529; cursor: grab;">

                {{-- Contrôles de Zoom --}}
                <div class="position-absolute top-0 end-0 m-3 d-flex flex-column gap-2" style="z-index: 1000;">
                    <button class="btn btn-secondary shadow-sm btn-sm" onclick="zoomMap(0.2)"><i class="bi bi-plus-lg"></i></button>
                    <button class="btn btn-secondary shadow-sm btn-sm" onclick="zoomMap(-0.2)"><i class="bi bi-dash-lg"></i></button>
                    <button class="btn btn-outline-light shadow-sm btn-sm" onclick="resetMap()" title="Recentrer"><i class="bi bi-arrows-fullscreen"></i></button>
                </div>

                {{-- Contenu transformable --}}
                <div id="map-content" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; transform-origin: center;">

                    @php
                        // On utilise la variable $gameId passée lors de l'include
                        $gamePlayers = \App\Models\Player::where('game_id', $gameId)->get();

                        // Tri déterministe
                        $gamePlayers = $gamePlayers->sortBy(function($p) {
                            return crc32($p->nom);
                        })->values();

                        $totalPlayers = $gamePlayers->count();
                        $radius = 250;
                        $containerSize = 800;
                        $centerX = $containerSize / 2;
                        $centerY = $containerSize / 2;

                        $coords = [];
                        foreach($gamePlayers as $index => $p) {
                            $angle = ($index / $totalPlayers) * 2 * pi() - (pi() / 2);
                            $coords[] = [
                                'x' => $centerX + $radius * cos($angle),
                                'y' => $centerY + $radius * sin($angle),
                                'id' => $p->id,
                                'player' => $p
                            ];
                        }
                    @endphp

                    <div style="position: relative; width: {{ $containerSize }}px; height: {{ $containerSize }}px; flex-shrink: 0;">

                        {{-- Décoration --}}
                        <div class="position-absolute top-50 start-50 translate-middle text-secondary opacity-25" style="pointer-events: none;">
                            <i class="bi bi-fire" style="font-size: 3rem;"></i>
                        </div>

                        {{-- Connecteurs --}}
                        <svg id="label-connectors" width="{{ $containerSize }}" height="{{ $containerSize }}" style="position: absolute; top: 0; left: 0; z-index: 5; pointer-events: none;"></svg>

                        {{-- Arêtes --}}
                        <svg width="{{ $containerSize }}" height="{{ $containerSize }}" style="position: absolute; top: 0; left: 0; z-index: 0; pointer-events: none;">
                            @if($totalPlayers > 1)
                                @foreach($coords as $i => $coord)
                                    @php
                                        $nextIndex = ($i + 1) % $totalPlayers;
                                        $nextCoord = $coords[$nextIndex];
                                    @endphp
                                    <line x1="{{ $coord['x'] }}" y1="{{ $coord['y'] }}"
                                          x2="{{ $nextCoord['x'] }}" y2="{{ $nextCoord['y'] }}"
                                          stroke="rgba(255,255,255,0.25)" stroke-width="2" />
                                @endforeach
                            @endif
                        </svg>

                        {{-- Points (Ancres) --}}
                        @foreach($coords as $item)
                            @php $p = $item['player']; @endphp
                            <div class="map-anchor"
                                 data-id="{{ $item['id'] }}"
                                 data-x="{{ $item['x'] }}"
                                 data-y="{{ $item['y'] }}"
                                 style="position: absolute; left: {{ $item['x'] }}px; top: {{ $item['y'] }}px; width: 24px; height: 24px; transform: translate(-50%, -50%); z-index: 10;">
                                <div class="rounded-circle border border-2 border-white shadow d-flex align-items-center justify-content-center w-100 h-100"
                                     style="background-color: {{ $p->is_alive ? '#198754' : '#dc3545' }}; cursor: pointer;"
                                     title="{{ $p->nom }} ({{ $p->is_alive ? 'Vivant' : 'Mort' }})">
                                     {{-- Indicateur "Vous" si l'ID correspond --}}
                                     @if(isset($currentPlayerId) && $p->id === $currentPlayerId)
                                        <div class="bg-info rounded-circle" style="width: 8px; height: 8px;"></div>
                                     @endif
                                </div>
                            </div>
                        @endforeach

                        {{-- Noms (Labels) --}}
                        @foreach($coords as $item)
                            <div class="map-label badge bg-dark bg-opacity-75 text-light shadow-sm border border-secondary"
                                 id="label-{{ $item['id'] }}"
                                 data-anchor-id="{{ $item['id'] }}"
                                 style="position: absolute; font-size: 0.85rem; padding: 4px 8px; z-index: 20; white-space: nowrap; transform-origin: center;">
                                {{ $item['player']->nom }}
                                @if (!$item['player']->is_alive)
                                    <br/>
                                    <span class="badge">{{ $item['player']->role ? $item['player']->role->nom : '' }}</span>
                                @endif
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>

            <div class="modal-footer border-top border-secondary justify-content-between py-2 bg-black bg-opacity-25">
                <small class="text-secondary fst-italic ms-2"><i class="bi bi-mouse me-1"></i>Zoom & Pan</small>
                <small class="text-muted me-2">
                    @if(isset($currentPlayerId))
                    <span class="badge bg-info me-1" style="width:10px;height:10px;padding:0;border-radius:50%;"> </span><p class="text-primary d-inline">Vous</p>
                    @endif
                    <span class="badge bg-success ms-2 me-1" style="width:10px;height:10px;padding:0;border-radius:50%;"> </span><p class="text-primary d-inline">Vivant</p>
                    <span class="badge bg-danger ms-2 me-1" style="width:10px;height:10px;padding:0;border-radius:50%;"> </span><p class="text-primary d-inline">Mort</p>
                </small>
            </div>
        </div>
    </div>
</div>

<script>
    // Le script est inclus ici pour être chargé avec le modal
    document.addEventListener('DOMContentLoaded', () => {
        const viewport = document.getElementById('map-viewport');
        if(!viewport) return; // Sécurité si le modal n'est pas présent

        const content = document.getElementById('map-content');
        const svgConnectors = document.getElementById('label-connectors');
        const labels = Array.from(document.querySelectorAll('.map-label'));
        const anchors = Array.from(document.querySelectorAll('.map-anchor'));

        let state = { scale: 1, panning: false, pointX: 0, pointY: 0, startX: 0, startY: 0 };

        function updateTransform() {
            content.style.transform = `translate(${state.pointX}px, ${state.pointY}px) scale(${state.scale})`;
            const inverseScale = 1 / state.scale;
            labels.forEach(lbl => {
                lbl.style.transform = `translate(-50%, -50%) scale(${inverseScale})`;
            });
        }

        // --- EVENTS ---
        viewport.addEventListener('wheel', (e) => {
            e.preventDefault();
            const xs = (e.clientX - state.pointX) / state.scale;
            const ys = (e.clientY - state.pointY) / state.scale;
            const nextScale = (-Math.sign(e.deltaY) > 0) ? state.scale * 1.1 : state.scale / 1.1;
            if (nextScale > 0.2 && nextScale < 5) {
                state.pointX = e.clientX - xs * nextScale;
                state.pointY = e.clientY - ys * nextScale;
                state.scale = nextScale;
                updateTransform();
            }
        });

        viewport.addEventListener('mousedown', (e) => { e.preventDefault(); state.startX = e.clientX - state.pointX; state.startY = e.clientY - state.pointY; state.panning = true; viewport.style.cursor = 'grabbing'; });
        viewport.addEventListener('mousemove', (e) => { if (!state.panning) return; e.preventDefault(); state.pointX = e.clientX - state.startX; state.pointY = e.clientY - state.startY; updateTransform(); });
        viewport.addEventListener('mouseup', () => { state.panning = false; viewport.style.cursor = 'grab'; });
        viewport.addEventListener('mouseleave', () => { state.panning = false; viewport.style.cursor = 'grab'; });
        viewport.addEventListener('touchstart', (e) => { if(e.touches.length===1){ state.startX = e.touches[0].clientX - state.pointX; state.startY = e.touches[0].clientY - state.pointY; state.panning = true; } });
        viewport.addEventListener('touchmove', (e) => { if(state.panning && e.touches.length===1){ e.preventDefault(); state.pointX = e.touches[0].clientX - state.startX; state.pointY = e.touches[0].clientY - state.startY; updateTransform(); } });
        viewport.addEventListener('touchend', () => { state.panning = false; });

        window.zoomMap = (delta) => { const s = state.scale + delta; if(s > 0.2 && s < 5) { state.scale = s; updateTransform(); } };
        window.resetMap = () => { state.scale = 1; state.pointX = 0; state.pointY = 0; updateTransform(); };

        // --- PHYSIQUE ---
        const nodes = labels.map(lbl => {
            const anchor = anchors.find(a => a.dataset.id === lbl.dataset.anchorId);
            const ax = parseFloat(anchor.dataset.x);
            const ay = parseFloat(anchor.dataset.y);
            const center = 400;
            const dx = ax - center;
            const dy = ay - center;
            const dist = Math.sqrt(dx*dx + dy*dy);
            const offset = 40;

            return { el: lbl, x: ax + (dx/dist) * offset, y: ay + (dy/dist) * offset, ax: ax, ay: ay };
        });

        const anchorNodes = anchors.map(a => ({ x: parseFloat(a.dataset.x), y: parseFloat(a.dataset.y) }));

        function solvePhysics() {
            let moved = false;
            for(let step=0; step<3; step++) {
                nodes.forEach(node => {
                    let fx = 0, fy = 0;

                    // Attraction Ancre
                    const dx = node.x - node.ax;
                    const dy = node.y - node.ay;
                    const dist = Math.sqrt(dx*dx + dy*dy);
                    if (dist > 45) {
                        fx -= (dx / dist) * (dist - 45) * 0.05;
                        fy -= (dy / dist) * (dist - 45) * 0.05;
                    }

                    // Répulsion Labels
                    nodes.forEach(other => {
                        if (node === other) return;
                        const diffX = node.x - other.x;
                        const diffY = node.y - other.y;
                        const d2 = diffX*diffX + diffY*diffY;
                        const minDist = 60;
                        if (d2 < minDist*minDist && d2 > 0) {
                            const d = Math.sqrt(d2);
                            const force = (minDist - d) * 0.5;
                            fx += (diffX / d) * force;
                            fy += (diffY / d) * force;
                        }
                    });

                    // Répulsion Ancres
                    anchorNodes.forEach(anc => {
                        const diffX = node.x - anc.x;
                        const diffY = node.y - anc.y;
                        const d2 = diffX*diffX + diffY*diffY;
                        const safeDist = 35;
                        if (d2 < safeDist*safeDist && d2 > 0) {
                            const d = Math.sqrt(d2);
                            const force = (safeDist - d) * 0.8;
                            fx += (diffX / d) * force;
                            fy += (diffY / d) * force;
                        }
                    });

                    if (Math.abs(fx) > 0.1 || Math.abs(fy) > 0.1) {
                        node.x += fx;
                        node.y += fy;
                        moved = true;
                    }
                });
            }

            drawConnectors();
            nodes.forEach(node => {
                node.el.style.left = node.x + 'px';
                node.el.style.top = node.y + 'px';
            });

            if (moved) requestAnimationFrame(solvePhysics);
        }

        function drawConnectors() {
            let svgContent = '';
            nodes.forEach(node => {
                const dx = node.x - node.ax;
                const dy = node.y - node.ay;
                const dist = Math.sqrt(dx*dx + dy*dy);
                if (dist > 30) {
                    svgContent += `<line x1="${node.ax}" y1="${node.ay}" x2="${node.x}" y2="${node.y}" stroke="rgba(255,255,255,0.4)" stroke-width="1" />`;
                }
            });
            svgConnectors.innerHTML = svgContent;
        }

        const modal = document.getElementById('graphModal');
        if(modal) {
            modal.addEventListener('shown.bs.modal', () => {
                resetMap();
                solvePhysics();
            });
        }
        solvePhysics();
    });
</script>
