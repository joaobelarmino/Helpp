<?php

/**
 * PPDAO.class [ TIPO ]
 * Descricao
 * @copyright (c) year, Geovana M. Melo 
 */
$_SESSION["erro"] = 0;
class PPDAO{
    private $resultado;
    
    public function cadastrar(PP $pp)
    {
        $query = "INSERT INTO pp (aluno_rmAluno, disciplina_CodDisciplina, gestor_rmGestor, cursoPP"
                . ", semestrePP, anoPP, seriePP, statusPP, disciplinaPP) values (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $cadastrar = Conn::getConn()->prepare($query);
        $cadastrar->bindValue(1, $pp->getRmAluno());
        $cadastrar->bindValue(2, $pp->getCodDisciplina());
        $cadastrar->bindValue(3, $pp->getRmGestor());
        $cadastrar->bindValue(4, $pp->getCursoPP());
        $cadastrar->bindValue(5, $pp->getSemestrePP());
        $cadastrar->bindValue(6, $pp->getAnoPP());
        $cadastrar->bindValue(7, $pp->getSeriePP());
        $cadastrar->bindValue(8, $pp->getStatusPP());
        $cadastrar->bindValue(9, $pp->getDisciplinaPP());
        
        try{
            $cadastrar->execute();
            $_SESSION["erro"] = 1;
            return true;
            //$this->result = Conn::getConn()->lastInsertId();
        } catch (Exception $e) {
            //$this->result = null;
            $_SESSION["erro"] = 2;
            WSErro("<b>Erro ao cadastrar:</b> {$e->getMessage()}", $e->getCode());
        }
    }
    
    public function consultar(){
        $sql = "SELECT b.aluno_rmAluno, a.nomeUsuario, b.seriePP, b.disciplinaPP, b.semestrePP, b.anoPP FROM usuario a inner join "
                . "pp b on a.rmUsuario = b.aluno_rmALuno order by a.nomeUsuario ";
        
        $consultar = Conn::getConn()->prepare($sql);
        $consultar->execute();
        
        if ($consultar->rowCount() > 0){
            $this->resultado = $consultar->fetchAll(PDO::FETCH_ASSOC);
            return $this->resultado;
        } 
    }
    
    public function consultarNomeAluno() {
        $sql = "SELECT a.nomeUsuario from usuario a inner join pp b on a.rmUsuario = b.aluno_rmAluno";
        
        $consultar = Conn::getConn()->prepare($sql);
        $consultar->execute();
        
        if ($consultar->rowCount() > 0){
            $this->resultado = $consultar->fetchAll(PDO::FETCH_ASSOC);
            return $this->resultado;
        } 
    }
    
    public function alterar(Disciplina $disciplina)
    {
        $query = "UPDATE disciplina SET nomeDisciplina = ?, siglaDisciplina = ? WHERE codDisciplina = ?";
        $alterar = Conn::getConn()->prepare($query);
        
        $alterar->bindValue(1, $disciplina->getNomeDisciplina());
        $alterar->bindValue(2, $disciplina->getSiglaDisciplina());
        
        $alterar->bindValue(3, $disciplina->getCodDisciplina());
        
        try{
            $alterar->execute();
            $this->result = Conn::getConn()->lastInsertId();
        } catch (Exception $e) {
            $this->result = null;
            WSErro("<b>Erro ao alterar o registro de código: {$disciplina->getCodDisciplina()}:</b> {$e->getMessage()}", $e->getCode());
        }
    }
    
    public function excluir($codDisciplina){
        $query = "DELETE FROM PP where codDisciplina = ?";
        
        $deletar = Conn::getConn()->prepare($query);
        $deletar->bindValue(1, $codDisciplina);
        $deletar->execute();
    }
    
    public function verificaDisciplina(Disciplina $disciplina) {

    }
    
    public function verificaTurma(PP $pp) {
        $query = "SELECT * FROM pp WHERE aluno_rmAluno = ? and disciplina_codDisciplina = ?";
        
        $verifica = Conn::getConn()->prepare($query);
        $verifica->bindValue(1, $pp->getRmAluno());
        $verifica->bindValue(2, $pp->getCodDisciplina());
        $verifica->execute();
        
        if($verifica->rowCount() > 0){
            $this->resultado = $verifica->fetchAll(PDO::FETCH_ASSOC);
            return $this->resultado;
        }else{
            $this->resultado = false;
        }
    }
    
    public function buscarProfPP($rmProfessor) {
        $query = "Select seriePP, a.nomeUsuario, disciplinaPP, statusPP from usuario a inner join pp b 
            on a.rmUsuario = b.aluno_rmAluno inner join professor_pp c 
            on b.aluno_rmAluno = c.cod_pp_rmAluno and b.disciplina_codDisciplina = c.cod_pp_codDisciplina 
            where c.rm_professor = ?";
       
        $busca = Conn::getConn()->prepare($query);
        $busca->bindValue(1, $rmProfessor);
        $busca->execute();
        
        $this->resultado = $busca->fetchAll(PDO::FETCH_ASSOC);
        return $this->resultado;
    }
    
    //Cadastro de competências, habilidades e bases tecnológicas
    public function preencherDoc31 (PP $pp)
    {
        $query = "UPDATE pp SET habilidadePP = ?, conhecimentoPP = ?, tecnologiaPP = ? WHERE aluno_rmAluno = ? "
                . "AND disciplina_codDisciplina = ?";
        
        $atualizar = Conn::getConn()->prepare($query);
        
        $atualizar->bindValue(1, $pp->getHabilidadePP());
        $atualizar->bindValue(2, $pp->getCompetenciaPP());
        $atualizar->bindValue(3, $pp->getBaseTecnologicaPP());
        
        $atualizar->bindValue(4, $pp->getRmAluno());
        $atualizar->bindValue(5, $pp->getCodDisciplina());
        
        try{
            $atualizar->execute();
            //echo "Competências, Habilidades e Bases Tecnológicas cadastradas com sucesso!";
            $this->resultado = true;
            //$this->result = Conn::getConn()->lastInsertId();
        } catch (Exception $e) {
            //$this->result = null;
            WSErro("<b>Erro ao cadastrar:</b> {$e->getMessage()}", $e->getCode());
        }
        
    }
    
    //Caso o professor queira editar alguma coisa no doc
    protected function alterarDoc31(Professor $professor){
        
    }
    
    //No fim do ano ele verificará se o aluno cumpriu ou não a PP
    protected function concluirDoc31(Professor $professor){
        
    }
    
    public function getResultado() {
        return $this->resultado;
    }
}