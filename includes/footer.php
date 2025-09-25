</div> <!-- Cierra el .container -->

<!-- Espacio entre contenido y footer -->
<div class="mb-4"></div>

<!-- Footer moderno -->
<footer class="bg-light border-top" style="
    border-color: #e9ecef;
    background-color: #f8f9fa;
    color: #495057;
    padding: 20px 0;
    font-size: 1rem;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
">
    <div class="container text-center">
        <span style="font-weight: 600; font-size: 1.1rem;">Smart Sales System</span>
        <span style="color: #007BFF; margin-left: 8px; font-weight: 500;">• Developed by SCROLL IND</span>
    </div>
</footer>

<!-- Scripts globales -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= $base_url ?>/js/funciones.js"></script>

<!-- Búsqueda en órdenes (solo si existe) -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const buscarOrden = document.getElementById('buscarOrden');
        if (buscarOrden) {
            buscarOrden.addEventListener('keyup', function() {
                const filtro = this.value.toLowerCase().trim();
                const filas = document.querySelectorAll('.orden-row');

                filas.forEach(fila => {
                    const texto = fila.getAttribute('data-buscar') || '';
                    if (texto.includes(filtro)) {
                        fila.style.display = '';
                    } else {
                        fila.style.display = 'none';
                    }
                });
            });
        }
    });
</script>

<!-- Estilos para evitar que el footer tape el contenido -->
<style>
    body {
        padding-bottom: 80px; /* Espacio suficiente para el footer */
    }

    @media (max-width: 768px) {
        body {
            padding-bottom: 90px;
        }
    }

    /* Asegura que el footer tenga altura mínima */
    footer {
        min-height: 60px;
        line-height: normal;
    }
</style>
</body>
</html>