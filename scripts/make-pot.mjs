import { readdir, readFile, writeFile } from 'node:fs/promises';
import path from 'node:path';

const domain = 'alynt-account-gateway';
const root = process.cwd();
const output = path.join(root, 'languages', `${domain}.pot`);
const includeExtensions = new Set(['.php']);
const excludedDirs = new Set(['.git', 'assets', 'build', 'node_modules', 'tests', 'vendor']);
const singularFunctions = [
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

function translatorComment(source, index) {
	const before = source.slice(Math.max(0, index - 600), index);
	const matches = [...before.matchAll(/\/\*\s*translators:\s*((?:(?!\*\/)[\s\S])*)\*\//gi)];
	const match = matches.at(-1);
	const trailing = match ? before.slice(match.index + match[0].length) : '';

	return match && trailing.length <= 200 ? match[1].replace(/\s+/g, ' ').trim() : '';
}

function addEntry(catalog, source, relativePath, match, msgid, options = {}) {
	const context = options.context || '';
	const plural = options.plural || '';
	const key = JSON.stringify([context, msgid, plural]);
	const line = source.slice(0, match.index).split('\n').length;

	if (!catalog.has(key)) {
		catalog.set(key, {
			context,
			msgid,
			plural,
			references: new Set(),
			comments: new Set(),
		});
	}

	const entry = catalog.get(key);
	const comment = translatorComment(source, match.index);
	entry.references.add(`${relativePath}:${line}`);
	if (comment) {
		entry.comments.add(comment);
	}
}

function collectStrings(source, relativePath, catalog) {
	const escapedDomain = domain.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
	const singularPattern = singularFunctions.join('|');
	const singular = new RegExp(`(?:${singularPattern})\\(\\s*(['"])((?:\\\\.|(?!\\1).)*)\\1\\s*,\\s*['"]${escapedDomain}['"]`, 'gs');
	const contextual = new RegExp(`(?:_x|_ex)\\(\\s*(['"])((?:\\\\.|(?!\\1).)*)\\1\\s*,\\s*(['"])((?:\\\\.|(?!\\3).)*)\\3\\s*,\\s*['"]${escapedDomain}['"]`, 'gs');
	const plural = new RegExp(`_n\\(\\s*(['"])((?:\\\\.|(?!\\1).)*)\\1\\s*,\\s*(['"])((?:\\\\.|(?!\\3).)*)\\3[\\s\\S]{0,500}?,\\s*['"]${escapedDomain}['"]\\s*\\)`, 'gs');
	const contextualPlural = new RegExp(`_nx\\(\\s*(['"])((?:\\\\.|(?!\\1).)*)\\1\\s*,\\s*(['"])((?:\\\\.|(?!\\3).)*)\\3[\\s\\S]{0,500}?,\\s*(['"])((?:\\\\.|(?!\\5).)*)\\5\\s*,\\s*['"]${escapedDomain}['"]\\s*\\)`, 'gs');
	let match;

	while ((match = singular.exec(source)) !== null) {
		addEntry(catalog, source, relativePath, match, decodeString(match[2]));
	}

	while ((match = contextual.exec(source)) !== null) {
		addEntry(catalog, source, relativePath, match, decodeString(match[2]), {
			context: decodeString(match[4]),
		});
	}

	while ((match = plural.exec(source)) !== null) {
		addEntry(catalog, source, relativePath, match, decodeString(match[2]), {
			plural: decodeString(match[4]),
		});
	}

	while ((match = contextualPlural.exec(source)) !== null) {
		addEntry(catalog, source, relativePath, match, decodeString(match[2]), {
			plural: decodeString(match[4]),
			context: decodeString(match[6]),
		});
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

const entries = [...catalog.values()].sort((left, right) => {
	return `${left.context}\0${left.msgid}`.localeCompare(`${right.context}\0${right.msgid}`);
});
const body = entries.flatMap((entry) => {
	const lines = [];

	if (entry.comments.size) {
		lines.push([...entry.comments].sort().map((comment) => `#. translators: ${comment}`).join('\n'));
	}
	lines.push([...entry.references].sort().map((ref) => `#: ${ref}`).join('\n'));
	if (entry.context) {
		lines.push(formatPotString(entry.context, 'msgctxt'));
	}
	lines.push(formatPotString(entry.msgid));
	if (entry.plural) {
		lines.push(formatPotString(entry.plural, 'msgid_plural'));
		lines.push('msgstr[0] ""', 'msgstr[1] ""');
	} else {
		lines.push('msgstr ""');
	}
	lines.push('');

	return lines;
});

const content = `${header.concat(body).join('\n')}\n`.replace(/\n+$/, '\n');

await writeFile(output, content, 'utf8');
console.log(`Wrote ${normalizePath(path.relative(root, output))} with ${entries.length} strings.`);
