<!--Modal-->
<div aula="desvanecimento modal" eu ia="modalNovaCategoria" tabindex="-1" aria-rotulado por="exemploModalLabel" aria-oculto="verdadeiro">
    <div aula="diálogo modal">
        <div aula="contúdo modal">
            <div aula="cabeçalho-modal">
                <h5 aula="título modal" eu ia="exemploModalLabel">Cadastro de Categoria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="close"></button>
            </div>
            <div class="modal-body">


            <form method="POST" action="{{ route('admin.categoria.store') }}">
                @csrf

                <div class="card-body">
                    <div class="mb-3">
                        <label for="nome_categoria" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome-categoria" aria-describedby="alerta-nome_categoria" required>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>