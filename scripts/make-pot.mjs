import { readdir, readFile, writeFile } from 'node:fs/promises';
import path from 'node:path';

const domain = 'alynt-account-gateway';
const root = process.cwd();
const output = path.join(root, 'languages', `${domain}.pot`);
const includeExtensions = new Set(['.php']);
const excludedDirs = new Set(['.git', 'assets', 'build', 'node_modules', 'tests', 'vendor']);
const functions = [
	'__',
	'_e',
	'esc_attr__',
	'esc_attr_e',
	'esc_html__',
	'esc_html_e',
];

function normalizePath(file) {
	return file.replaceAll(path.sep, '/');
}

function decodeString(value) {
	return value
		.replace(/\\n/g, '\n')
		.replace(/\\r/g, '\r')
		.replace(/\\t/g, '\t')
		.replace(/\\'/g, "'")
		.replace(/\\"/g, '"')
		.replace(/\\\\/g, '\\');
}

function escapePot(value) {
	return value
		.replace(/\\/g, '\\\\')
		.replace(/"/g, '\\"')
		.replace(/\t/g, '\\t');
}

function formatPotString(value, key = 'msgid') {
	if (!value.includes('\n')) {
		return `${key} "${escapePot(value)}"`;
	}

	const lines = value.split('\n');
	return [
		`${key} ""`,
		...lines.map((line, index) => {
			const suffix = index === lines.length - 1 ? '' : '\\n';
			return `"${escapePot(line)}${suffix}"`;
		}),
	].join('\n');
}

async function listFiles(dir) {
	const entries = await readdir(dir, { withFileTypes: true });
	const files = [];

	for (const entry of entries) {
		if (entry.isDirectory()) {
			if (!excludedDirs.has(entry.name)) {
				files.push(...await listFiles(path.join(dir, entry.name)));
			}
			continue;
		}

		if (includeExtensions.has(path.extname(entry.name))) {
			files.push(path.join(dir, entry.name));
		}
	}

	return files;
}

function collectStrings(source, relativePath, catalog) {
	const functionPattern = functions.map((name) => name.replaceAll('_', '_')).join('|');
	const pattern = new RegExp(`(?:${functionPattern})\\(\\s*(['"])((?:\\\\.|(?!\\1).)*)\\1\\s*,\\s*['"]${domain}['"]`, 'gs');
	let match;

	while ((match = pattern.exec(source)) !== null) {
		const msgid = decodeString(match[2]);
		const before = source.slice(0, match.index);
		const line = before.split('\n').length;

		if (!catalog.has(msgid)) {
			catalog.set(msgid, new Set());
		}

		catalog.get(msgid).add(`${relativePath}:${line}`);
	}
}

const catalog = new Map();
const mainPluginFile = await readFile(path.join(root, 'alynt-account-gateway.php'), 'utf8');
const versionMatch = mainPluginFile.match(/define\(\s*'ALYNT_AG_VERSION'\s*,\s*'([^']+)'\s*\)/);
const version = versionMatch ? versionMatch[1] : '0.1.0';
const files = await listFiles(root);

for (const file of files) {
	const relativePath = normalizePath(path.relative(root, file));
	const source = await readFile(file, 'utf8');
	collectStrings(source, relativePath, catalog);
}

const now = new Date().toISOString().slice(0, 16).replace('T', ' ');
const header = [
	'# Copyright (C) 2026 Alynt',
	'# This file is distributed under the GPL-2.0-or-later.',
	'msgid ""',
	'msgstr ""',
	`"Project-Id-Version: Alynt Account Gateway ${version}\\n"`,
	'"Report-Msgid-Bugs-To: \\n"',
	`"POT-Creation-Date: ${now}+0000\\n"`,
	'"MIME-Version: 1.0\\n"',
	'"Content-Type: text/plain; charset=UTF-8\\n"',
	'"Content-Transfer-Encoding: 8bit\\n"',
	'"X-Generator: scripts/make-pot.mjs\\n"',
	`"X-Domain: ${domain}\\n"`,
	'',
];

const entries = [...catalog.entries()].sort(([left], [right]) => left.localeCompare(right));
const body = entries.flatMap(([msgid, refs]) => [
	[...refs].sort().map((ref) => `#: ${ref}`).join('\n'),
	formatPotString(msgid),
	'msgstr ""',
	'',
]);

const content = `${header.concat(body).join('\n')}\n`.replace(/\n+$/, '\n');

await writeFile(output, content, 'utf8');
console.log(`Wrote ${normalizePath(path.relative(root, output))} with ${entries.length} strings.`);
