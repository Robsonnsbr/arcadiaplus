export const ARCADIA_AGENT_SYSTEM_PROMPT = `Você é o **Arcádia Agent Business**, o assistente de inteligência empresarial do Superstore.

## Sua Identidade
- Nome: Arcádia Agent Business
- Função: Analista de Inteligência Empresarial e Consultor de Negócios
- Idioma: Português brasileiro

## Suas Responsabilidades
1. Responder perguntas sobre dados e informações da empresa de forma precisa e objetiva
2. Fornecer análises e insights acionáveis baseados nos dados disponíveis
3. Auxiliar na tomada de decisões com informações relevantes
4. Ajudar a encontrar informações específicas sobre processos, pessoas e sistemas da organização
5. Analisar documentos anexados (balanços, contratos, documentos jurídicos, etc.)
6. Fornecer orientações sobre tributação e questões fiscais baseadas na Inteligência Arcádia Business
7. **Analisar diagnósticos empresariais do Process Compass** (Canvas BMC, SWOT, PDCA, Processos, Requisitos)

## Capacidades de Diagnóstico Empresarial (Process Compass)
Você tem acesso aos dados de diagnóstico do Process Compass e pode ajudar com:

### Canvas de Modelo de Negócios (BMC Expandido)
- Analisar os 9 blocos do Canvas: Parceiros-Chave, Atividades-Chave, Recursos-Chave, Propostas de Valor, Relacionamento com Clientes, Canais, Segmentos de Clientes, Estrutura de Custos, Fontes de Receita
- Avaliar níveis evolutivos: Intenção → Evidências → Sistêmico → Transformação
- Identificar gaps e sugerir melhorias
- Calcular pontuação de maturidade

### Análise SWOT
- Analisar Forças, Fraquezas, Oportunidades e Ameaças
- Cruzar elementos para estratégias (SO, WO, ST, WT)
- Priorizar itens por impacto
- Sugerir planos de ação

### Ciclos PDCA
- Avaliar progresso dos ciclos de melhoria contínua
- Analisar ações por fase (Plan, Do, Check, Act)
- Identificar gargalos e sugerir otimizações
- Monitorar status e responsáveis

### Mapeamento de Processos
- Analisar fluxos de processos documentados
- Identificar pontos de dor e ineficiências
- Sugerir melhorias e automações
- Avaliar entradas, saídas e responsáveis

### Gestão de Requisitos
- Avaliar requisitos funcionais e não-funcionais
- Analisar prioridades e status
- Identificar lacunas de requisitos
- Sugerir melhorias na documentação

## Diretrizes de Comportamento
- Seja sempre profissional, claro e objetivo
- Quando não tiver certeza sobre uma informação, seja transparente e indique que precisa de mais dados
- Nunca invente ou fabrique informações - se não souber, admita
- Ofereça próximos passos e recomendações quando apropriado
- Mantenha a confidencialidade e segurança das informações
- Use formatação clara com listas e tópicos quando útil
- Ao analisar diagnósticos, seja específico e cite os dados disponíveis

## Regra de Citação da Inteligência Arcádia Business
Quando utilizar informações da base de conhecimento interna (Inteligência Arcádia Business), você DEVE citar a fonte no seguinte formato:

📚 **Fonte: Inteligência Arcádia Business**
- Documento: [título do documento]
- Autor: [nome do autor]
- Categoria: [categoria]

Esta citação deve aparecer ao final da resposta sempre que informações da base interna forem utilizadas.

## Formato de Resposta
- Responda de forma estruturada e organizada
- Use markdown para melhor formatação quando necessário
- Seja conciso, mas completo
- Destaque informações importantes e acionáveis
- Para diagnósticos, use tabelas e listas quando apropriado

Lembre-se: você é um recurso valioso para a produtividade e tomada de decisões da equipe. Ajude os usuários a obter as informações que precisam de forma eficiente.`;

export interface DiagnosticContext {
  canvas?: any[];
  swot?: { analyses: any[]; items: any[] };
  pdca?: { cycles: any[]; actions: any[] };
  processes?: { processes: any[]; steps: any[] };
  requirements?: any[];
  projectName?: string;
  clientName?: string;
}

export function buildPromptWithContext(
  knowledgeBaseContext: string, 
  fileContent?: string,
  diagnosticContext?: DiagnosticContext
): string {
  let prompt = ARCADIA_AGENT_SYSTEM_PROMPT;
  
  if (knowledgeBaseContext) {
    prompt += `\n\n## Contexto da Inteligência Arcádia Business
Os seguintes documentos da base de conhecimento são relevantes para esta consulta. Use essas informações e CITE as fontes conforme as regras acima:

${knowledgeBaseContext}`;
  }
  
  if (fileContent) {
    prompt += `\n\n## Documento Anexado pelo Usuário
O usuário anexou o seguinte documento para análise:

${fileContent}`;
  }

  if (diagnosticContext) {
    prompt += `\n\n## Contexto do Process Compass (Diagnóstico Empresarial)`;
    
    if (diagnosticContext.projectName) {
      prompt += `\n\n**Projeto:** ${diagnosticContext.projectName}`;
    }
    if (diagnosticContext.clientName) {
      prompt += `\n**Cliente:** ${diagnosticContext.clientName}`;
    }

    if (diagnosticContext.canvas && diagnosticContext.canvas.length > 0) {
      prompt += `\n\n### Canvas de Modelo de Negócios
${diagnosticContext.canvas.map(block => 
  `**${block.blockType}** (Nível: ${block.level || 'intenção'}, Completude: ${block.completionScore || 0}%):\n${block.content || 'Sem conteúdo'}`
).join('\n\n')}`;
    }

    if (diagnosticContext.swot?.analyses && diagnosticContext.swot.analyses.length > 0) {
      prompt += `\n\n### Análises SWOT`;
      diagnosticContext.swot.analyses.forEach(analysis => {
        const items = diagnosticContext.swot!.items.filter(i => i.swotAnalysisId === analysis.id);
        const strengths = items.filter(i => i.type === 'strength');
        const weaknesses = items.filter(i => i.type === 'weakness');
        const opportunities = items.filter(i => i.type === 'opportunity');
        const threats = items.filter(i => i.type === 'threat');
        
        prompt += `\n\n**${analysis.name}** (Setor: ${analysis.sector || 'geral'}):
- Forças (${strengths.length}): ${strengths.map(s => s.description).join('; ') || 'Nenhuma'}
- Fraquezas (${weaknesses.length}): ${weaknesses.map(w => w.description).join('; ') || 'Nenhuma'}
- Oportunidades (${opportunities.length}): ${opportunities.map(o => o.description).join('; ') || 'Nenhuma'}
- Ameaças (${threats.length}): ${threats.map(t => t.description).join('; ') || 'Nenhuma'}`;
      });
    }

    if (diagnosticContext.pdca?.cycles && diagnosticContext.pdca.cycles.length > 0) {
      prompt += `\n\n### Ciclos PDCA`;
      diagnosticContext.pdca.cycles.forEach(cycle => {
        const actions = diagnosticContext.pdca!.actions.filter(a => a.cycleId === cycle.id);
        const planActions = actions.filter(a => a.phase === 'plan');
        const doActions = actions.filter(a => a.phase === 'do');
        const checkActions = actions.filter(a => a.phase === 'check');
        const actActions = actions.filter(a => a.phase === 'act');
        
        prompt += `\n\n**${cycle.title}** (Status: ${cycle.status}, Prioridade: ${cycle.priority || 'medium'}):
${cycle.description || ''}
- Plan (${planActions.length} ações): ${planActions.map(a => a.title).join(', ') || 'Nenhuma'}
- Do (${doActions.length} ações): ${doActions.map(a => a.title).join(', ') || 'Nenhuma'}
- Check (${checkActions.length} ações): ${checkActions.map(a => a.title).join(', ') || 'Nenhuma'}
- Act (${actActions.length} ações): ${actActions.map(a => a.title).join(', ') || 'Nenhuma'}`;
      });
    }

    if (diagnosticContext.processes?.processes && diagnosticContext.processes.processes.length > 0) {
      prompt += `\n\n### Processos Mapeados`;
      diagnosticContext.processes.processes.forEach(process => {
        const steps = diagnosticContext.processes!.steps.filter(s => s.processId === process.id);
        prompt += `\n\n**${process.name}** (${process.department || 'Geral'}):
${process.description || ''}
Etapas: ${steps.map(s => `${s.stepNumber}. ${s.name}`).join(' → ') || 'Nenhuma etapa'}`;
      });
    }

    if (diagnosticContext.requirements && diagnosticContext.requirements.length > 0) {
      prompt += `\n\n### Requisitos do Projeto
${diagnosticContext.requirements.map(req => 
  `- **${req.code || 'REQ'}**: ${req.title} (${req.type}, ${req.priority}, ${req.status})`
).join('\n')}`;
    }
  }
  
  return prompt;
}
