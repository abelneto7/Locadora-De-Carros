<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Inicio do card de busca-->
                <card-component titulo="Busca de marcas">
                    <template v-slot:conteudo>
                        <div class="form-row">
                            <div class="col mb-3">
                                <input-container-component titulo="ID" id="inputId" id-help="idHelp" texto-ajuda="Opcional. Informe o ID da marca.">
                                    <input type="number" class="form-control" id="inputId" v-model="busca.id" aria-describedby="idHelp" placeholder="ID">
                                </input-container-component>
                            </div>
                            <div class="col mb-3">
                                <input-container-component titulo="Nome da marca" id="inputNome" id-help="nomeHelp" texto-ajuda="Opcional. Informe o nome da marca.">
                                    <input type="text" class="form-control" id="inputNome" v-model="busca.nome" aria-describedby="nomeHelp" placeholder="Nome da marca">
                                </input-container-component>
                            </div>
                        </div>
                    </template>

                    <template v-slot:rodape>
                        <button type="submit" class="btn btn-primary btn-sm float-right" @click="pesquisar()">Pesquisar</button>
                    </template>
                </card-component>
                <!-- Fim do card de busca-->

                <!-- Inicio do card de listagem-->
                <card-component titulo="Listagem de marcas">
                    <template v-slot:conteudo>
                        <table-component
                            :dados="marcas.data"
                            :visualizar="{visivel: true, dataToggle: 'modal', dataTarget: '#modalMarcaVisualizar'}"
                            :atualizar="{visivel: true, dataToggle: 'modal', dataTarget: '#modalMarcaAtualizar'}"
                            :remover="{visivel: true, dataToggle: 'modal', dataTarget: '#modalMarcaRemover'}"
                            :titulos = "{
                                id: {titulo: 'ID', tipo: 'texto'},
                                nome: {titulo: 'Nome', tipo: 'texto'},
                                imagem: {titulo: 'Imagem', tipo: 'imagem'},
                                created_at: {titulo: 'Criação', tipo: 'data'},
                            }"
                        ></table-component>
                    </template>

                    <template v-slot:rodape>

                        <div class="row">
                            <div class="col-10">
                                <paginate-component>
                                    <li v-for="l, key in marcas.links" :key="key"
                                        :class="l.active ? 'page-item active' : 'page-item'"
                                        @click="paginacao(l)"
                                    >
                                        <a class="page-link" v-html="l.label">
                                        </a>
                                    </li>
                                </paginate-component>
                            </div>

                            <div class="col">
                                <button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#modalMarca">Adicionar</button>
                            </div>
                        </div>
                    </template>
                </card-component>
                <!-- Fim do card de listagem-->
            </div>
        </div>

        <!-- Inicio do modal de inclusão de marca -->
        <modal-component id="modalMarca" titulo="Adicionar Marca">

            <template v-slot:alertas>
                <alert-component tipo="success" :detalhes="transacaoDetalhes" titulo="Cadastro realizado com sucesso" v-if="transacaoStatus == 'adicionado'"></alert-component>
                <alert-component tipo="danger" :detalhes="transacaoDetalhes" titulo="Erro ao tentar cadastrar a marca" v-if="transacaoStatus == 'erro'"></alert-component>
            </template>

            <template v-slot:conteudo>
                <div class="form-group">
                    <input-container-component titulo="Nome da marca" id="novoNome" id-help="novoNomeHelp" texto-ajuda="Informe o nome da marca.">
                        <input type="text" class="form-control" id="novoNome" aria-describedby="novoNomeHelp" placeholder="Nome da marca" v-model="nomeMarca">
                    </input-container-component>
                    {{ nomeMarca }}
                </div>

                <div class="form-group">
                    <input-container-component titulo="Imagem" id="novoImagem" id-help="novoImagemHelp" texto-ajuda="Selecione uma imagem no formato PNG.">
                        <input type="file" class="form-control-file" id="novoImagem" aria-describedby="novoImagemHelp" placeholder="Selecione uma imagem" @change="carregarImagem($event)">
                    </input-container-component>
                    {{ arquivoImagem }}
                </div>
            </template>

            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" @click="salvar()">Salvar</button>
            </template>
        </modal-component>
        <!-- Fim do modal de inclusão de marca -->

        <!-- Inicio do modal de visualização de marca -->
        <modal-component id="modalMarcaVisualizar" titulo="Visualizar Marca">
            <template v-slot:alertas>
            </template>

            <template v-slot:conteudo>
                <input-container-component titulo="ID">
                    <input type="text" class="form-control" :value="$store.state.item.id" disabled>
                </input-container-component>

                <input-container-component titulo="Nome da marca">
                    <input type="text" class="form-control" :value="$store.state.item.nome" disabled>
                </input-container-component>

                <input-container-component titulo="Imagem">
                    <img :src="'/storage/'+$store.state.item.imagem" v-if="$store.state.item.imagem" width="60" height="60">
                </input-container-component>

                <input-container-component titulo="Data de criação">
                    <input type="text" class="form-control" :value="$store.state.item.created_at" disabled>
                </input-container-component>
            </template>

            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </template>
        </modal-component>
        <!-- Fim do modal de visualização de marca -->

        <!-- Inicio do modal de remoção de marca -->
        <modal-component id="modalMarcaRemover" titulo="Remover Marca">
            <template v-slot:alertas>
                <alert-component tipo="success" titulo="Transação realizada com sucesso" :detalhes="$store.state.transacao" v-if="$store.state.transacao.status == 'sucesso'"></alert-component>
                <alert-component tipo="danger" titulo="Erro na transação" :detalhes="$store.state.transacao" v-if="$store.state.transacao.status == 'erro'"></alert-component>
            </template>

            <template v-slot:conteudo>
                <input-container-component titulo="ID">
                    <input type="text" class="form-control" :value="$store.state.item.id" disabled>
                </input-container-component>

                <input-container-component titulo="Nome da marca">
                    <input type="text" class="form-control" :value="$store.state.item.nome" disabled>
                </input-container-component>
            </template>

            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-danger" @click="remover()"
                v-if="$store.state.transacao.status != 'sucesso'">Remover</button>
            </template>
        </modal-component>
        <!-- Fim do modal de remoção de marca -->
         
        <!-- Inicio do modal de atualização de marca -->
        <modal-component id="modalMarcaAtualizar" titulo="Atualizar Marca">

        <template v-slot:alertas>
            <alert-component tipo="success" titulo="Transação realizada com sucesso" :detalhes="$store.state.transacao" v-if="$store.state.transacao.status == 'sucesso'"></alert-component>
            <alert-component tipo="danger" titulo="Erro na transação" :detalhes="$store.state.transacao" v-if="$store.state.transacao.status == 'erro'"></alert-component>
        </template>

        <template v-slot:conteudo>
            <div class="form-group">
                <input-container-component titulo="Nome da marca" id="atualizarNome" id-help="atualizarNomeHelp" texto-ajuda="Informe o nome da marca.">
                    <input type="text" class="form-control" id="atualizarNome" aria-describedby="atualizarNomeHelp" placeholder="Nome da marca" v-model="$store.state.item.nome">
                </input-container-component>
            </div>

            <div class="form-group">
                <input-container-component titulo="Imagem" id="atualizarImagem" id-help="atualizarImagemHelp" texto-ajuda="Selecione uma imagem no formato PNG.">
                    <input type="file" class="form-control-file" id="atualizarImagem" aria-describedby="atualizarImagemHelp" placeholder="Selecione uma imagem" @change="carregarImagem($event)">
                </input-container-component>
            </div>
        </template>

        <template v-slot:rodape>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            <button type="button" class="btn btn-primary" @click="atualizar()">Atualizar</button>
        </template>
        </modal-component>
        <!-- Fim do modal de atualização de marca -->

    </div>
</template>

<script>
import axios from 'axios';

export default {
    computed: {
        token() {

            let token = document.cookie.split(';').find(indice => {
                return indice.includes('token=')
            })

            token = token.split('=')[1]
            token = 'Bearer ' + token

            return token
        }
    },

    data() {
        return {
            urlBase: 'http://localhost:8012/api/v1/marca',
            urlPaginacao: '',
            urlFiltro: '',
            nomeMarca: '',
            arquivoImagem: [],
            transacaoStatus: '',
            transacaoDetalhes: {},
            marcas: {data:[]},
            busca: {id: '', nome: ''}
        }
    },

    methods: {
        atualizar() {
            let formData = new FormData();
            formData.append('_method', 'patch')
            formData.append('nome', this.$store.state.item.nome)

            if(this.arquivoImagem[0]){
                formData.append('imagem', this.arquivoImagem[0])
            }

            let url = this.urlBase+'/'+this.$store.state.item.id

            let config = {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'Accept': 'application/json',
                    'Authorization': this.token
                }
            }
            
            axios.post(url, formData, config)
                .then(response => {
                    this.$store.state.transacao.status = 'sucesso'
                    this.$store.state.transacao.mensagem = 'Registro de marca atualizado com sucesso.'

                    //Limpar campo de seleção de arquivos
                    atualizarImagem.value = ''
                    this.carregarLista()
                })
                .catch(errors => {
                    this.$store.state.transacao.status = 'erro'
                    this.$store.state.transacao.mensagem = errors.response.data.message
                    this.$store.state.transacao.dados = errors.response.data.errors                    
                })
        },
        remover(){
            let confirmacao = confirm('Tem certeza que deseja remover esse registro? ')

            if(!confirmacao){
                return false;
            }

            let formData = new FormData()
            formData.append('_method', 'delete')

            let config = {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': this.token
                }
            }

            let url = this.urlBase+'/'+this.$store.state.item.id

            axios.post(url, formData, config)
                .then(response => {
                    this.$store.state.transacao.status = 'sucesso'
                    this.$store.state.transacao.mensagem = response.data.msg

                    this.carregarLista()
                })
                .catch(errors => {
                    this.$store.state.transacao.status = 'erro'
                    this.$store.state.transacao.mensagem = errors.response.data.erro
                })
        },
        pesquisar(){
            let filtro = ''

            for (let chave in this.busca){
                if (this.busca[chave]){
                    if (filtro != ''){
                        filtro += ";"
                    }
                    filtro += chave + ':like:' +
                        '' + this.busca[chave]
                }
            }

            if (filtro != ''){
                this.urlPaginacao = 'page=1'
                this.urlFiltro = '&filtro='+filtro
            } else {
                this.urlFiltro = ''
            }

            this.carregarLista()
        },
        paginacao(l){
          if(l.url){
              this.urlPaginacao = l.url.split('?')[1]
              this.carregarLista()
          }
        },
        carregarLista(){
            let config = {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': this.token
                }
            }

            let url = this.urlBase + '?' + this.urlPaginacao + this.urlFiltro
            axios.get(url, config)
                .then(response => {
                    this.marcas = response.data
                })
                .catch(errors => {
                    console.log(errors)
                })
        },
        carregarImagem(e) {
            this.arquivoImagem = e.target.files
        },
        salvar() {
            console.log(this.nomeMarca, this.arquivoImagem[0])

            let formData = new FormData();
            formData.append('nome', this.nomeMarca)
            formData.append('imagem', this.arquivoImagem[0])

            let config = {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'Accept': 'application/json',
                    'Authorization': this.token
                }
            }

            axios.post(this.urlBase, formData, config)
                .then(response => {
                    this.transacaoStatus = 'adicionado'
                    this.transacaoDetalhes = {
                        mensagem: 'ID do registro: ' + response.data.id
                    }
                    console.log(response)
                })
                .catch(errors => {
                    this.transacaoStatus = 'erro'
                    this.transacaoDetalhes = {
                        mensagem: errors.response.data.message,
                        dados: errors.response.data.errors
                    }

                    //console.log(errors.response.data.message)
                })
        }
    },
    mounted() {
        this.carregarLista()
    }
}

</script>
