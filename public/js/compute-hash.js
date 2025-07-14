/**
 * Calcola l'hash SHA-256 della password fornita.
 */
async function computeHash(password) {
    const hashBuffer = await crypto.subtle.digest('SHA-256', new TextEncoder().encode(password));
    return Array
        // Converti l'ArrayBuffer in un array di byte
        .from(new Uint8Array(hashBuffer))
        // Mappa ogni byte in una stringa esadecimale
        .map(b => b.toString(16).padStart(2, '0')).join('');
}
