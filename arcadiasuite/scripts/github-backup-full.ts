/**
 * Script para backup COMPLETO do Superstore no GitHub
 * Cria um arquivo tar.gz e faz upload como release
 */

import { Octokit } from '@octokit/rest';
import * as fs from 'fs';
import * as path from 'path';
import { execSync } from 'child_process';

let connectionSettings: any;

async function getAccessToken() {
  if (connectionSettings && connectionSettings.settings.expires_at && new Date(connectionSettings.settings.expires_at).getTime() > Date.now()) {
    return connectionSettings.settings.access_token;
  }
  
  const hostname = process.env.REPLIT_CONNECTORS_HOSTNAME;
  const xReplitToken = process.env.REPL_IDENTITY 
    ? 'repl ' + process.env.REPL_IDENTITY 
    : process.env.WEB_REPL_RENEWAL 
    ? 'depl ' + process.env.WEB_REPL_RENEWAL 
    : null;

  if (!xReplitToken) throw new Error('Token não encontrado');

  connectionSettings = await fetch(
    'https://' + hostname + '/api/v2/connection?include_secrets=true&connector_names=github',
    {
      headers: {
        'Accept': 'application/json',
        'X_REPLIT_TOKEN': xReplitToken
      }
    }
  ).then(res => res.json()).then(data => data.items?.[0]);

  const accessToken = connectionSettings?.settings?.access_token || connectionSettings.settings?.oauth?.credentials?.access_token;
  if (!connectionSettings || !accessToken) throw new Error('GitHub não conectado');
  return accessToken;
}

async function getGitHubClient() {
  return new Octokit({ auth: await getAccessToken() });
}

const OWNER = 'JonasRodriguesPachceo';
const REPO = 'ArcadiaSuite-';

async function createFullBackup() {
  console.log('🚀 Iniciando BACKUP COMPLETO do Superstore\n');
  
  const projectRoot = process.cwd();
  const timestamp = new Date().toISOString().replace(/[:.]/g, '-').slice(0, 19);
  const backupName = `arcadia-suite-backup-${timestamp}`;
  const tarFile = `/tmp/${backupName}.tar.gz`;
  
  // Create exclusion list
  const excludeFile = '/tmp/backup-exclude.txt';
  const excludePatterns = [
    'node_modules',
    '.git',
    '.cache',
    '.upm',
    '.config',
    '.local',
    '.npm',
    '.nix-profile',
    '.pythonlibs',
    '__pycache__',
    '*.pyc',
    '.env',
    'plus/vendor',
    'plus/node_modules', 
    'plus/storage/logs/*',
    'plus/storage/framework/cache/*',
    'plus/storage/framework/sessions/*',
    'plus/storage/framework/views/*',
    'plus/bootstrap/cache/*',
    'attached_assets',
    'tmp',
    '*.log',
    '.breakpoints',
    'package-lock.json',
    'generated-icon.png',
  ];
  
  fs.writeFileSync(excludeFile, excludePatterns.join('\n'));
  
  console.log('📦 Criando arquivo de backup...');
  console.log('   (Isso pode demorar alguns minutos)\n');
  
  try {
    // Create tar.gz excluding unnecessary files
    execSync(`tar -czf ${tarFile} --exclude-from=${excludeFile} -C ${projectRoot} .`, {
      stdio: 'inherit',
      maxBuffer: 100 * 1024 * 1024
    });
  } catch (e) {
    // tar may return warnings but still work
  }
  
  const stats = fs.statSync(tarFile);
  const sizeMB = (stats.size / (1024 * 1024)).toFixed(2);
  console.log(`✅ Backup criado: ${sizeMB} MB\n`);
  
  // Connect to GitHub
  const octokit = await getGitHubClient();
  const { data: user } = await octokit.users.getAuthenticated();
  console.log(`✅ Autenticado como: ${user.login}`);
  
  // Create a release
  const tagName = `backup-${timestamp}`;
  const releaseName = `Backup Completo - ${new Date().toLocaleDateString('pt-BR')} ${new Date().toLocaleTimeString('pt-BR')}`;
  
  console.log('\n📤 Criando release no GitHub...');
  
  const { data: release } = await octokit.repos.createRelease({
    owner: OWNER,
    repo: REPO,
    tag_name: tagName,
    name: releaseName,
    body: `## Backup Completo do Superstore

**Data:** ${new Date().toLocaleString('pt-BR')}
**Tamanho:** ${sizeMB} MB

### Conteúdo incluído:
- ✅ Frontend React/TypeScript (client/)
- ✅ Backend Express/Node.js (server/)
- ✅ Schemas compartilhados (shared/)
- ✅ Laravel Plus ERP (plus/)
- ✅ Scripts Python (se houver)
- ✅ Documentação (docs/)
- ✅ Configurações do projeto

### Para restaurar:
\`\`\`bash
# Baixe o arquivo .tar.gz da release
tar -xzf arcadia-suite-backup-*.tar.gz
npm install
cd plus && composer install
\`\`\`
`,
    draft: false,
    prerelease: false
  });
  
  console.log(`✅ Release criada: ${release.html_url}\n`);
  
  // Upload the backup file as release asset
  console.log('📤 Fazendo upload do arquivo de backup...');
  console.log(`   (${sizeMB} MB - pode demorar)\n`);
  
  const fileContent = fs.readFileSync(tarFile);
  
  await octokit.repos.uploadReleaseAsset({
    owner: OWNER,
    repo: REPO,
    release_id: release.id,
    name: `${backupName}.tar.gz`,
    // @ts-ignore
    data: fileContent,
    headers: {
      'content-type': 'application/gzip',
      'content-length': stats.size
    }
  });
  
  // Cleanup
  fs.unlinkSync(tarFile);
  fs.unlinkSync(excludeFile);
  
  console.log('\n' + '='.repeat(50));
  console.log('🎉 BACKUP COMPLETO REALIZADO COM SUCESSO!');
  console.log('='.repeat(50));
  console.log(`\n📦 Tamanho: ${sizeMB} MB`);
  console.log(`🔗 Release: ${release.html_url}`);
  console.log(`📥 Download: https://github.com/${OWNER}/${REPO}/releases/tag/${tagName}`);
  console.log('\nPara restaurar, baixe o .tar.gz e extraia com:');
  console.log('  tar -xzf arcadia-suite-backup-*.tar.gz\n');
}

createFullBackup().catch(err => {
  console.error('❌ Erro:', err.message);
  process.exit(1);
});
