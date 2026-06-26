/**
 * Compress image files before upload while keeping the original resolution.
 */
(function () {
    const DEFAULT_QUALITY = 0.72;
    const MIN_BYTES_TO_COMPRESS = 100 * 1024;

    function replaceExtension(name, extension) {
        const base = name.replace(/\.[^.]+$/, '') || 'image';
        return `${base}.${extension}`;
    }

    async function loadImageSource(file) {
        if (typeof createImageBitmap === 'function') {
            const bitmap = await createImageBitmap(file);
            return {
                width: bitmap.width,
                height: bitmap.height,
                draw(ctx) {
                    ctx.drawImage(bitmap, 0, 0);
                },
                cleanup() {
                    bitmap.close();
                },
            };
        }

        return new Promise((resolve, reject) => {
            const url = URL.createObjectURL(file);
            const img = new Image();

            img.onload = () => {
                URL.revokeObjectURL(url);
                resolve({
                    width: img.naturalWidth,
                    height: img.naturalHeight,
                    draw(ctx) {
                        ctx.drawImage(img, 0, 0);
                    },
                    cleanup() {},
                });
            };

            img.onerror = () => {
                URL.revokeObjectURL(url);
                reject(new Error('Failed to decode image'));
            };

            img.src = url;
        });
    }

    function canvasToBlob(canvas, type, quality) {
        return new Promise((resolve) => {
            canvas.toBlob(resolve, type, quality);
        });
    }

    async function compressImageFile(file, options = {}) {
        if (!file || !file.type || !file.type.startsWith('image/')) {
            return file;
        }

        if (file.type === 'image/gif') {
            return file;
        }

        const minBytes = options.minBytes ?? MIN_BYTES_TO_COMPRESS;
        if (file.size < minBytes) {
            return file;
        }

        try {
            const source = await loadImageSource(file);
            const canvas = document.createElement('canvas');
            canvas.width = source.width;
            canvas.height = source.height;

            const ctx = canvas.getContext('2d', { alpha: true });
            if (!ctx) {
                source.cleanup();
                return file;
            }

            if (file.type === 'image/jpeg' || file.type === 'image/jpg') {
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
            }

            source.draw(ctx);
            source.cleanup();

            const keepPng = file.type === 'image/png';
            const keepWebp = file.type === 'image/webp';
            const quality = options.quality ?? DEFAULT_QUALITY;

            let outputType = 'image/jpeg';
            if (keepPng) {
                outputType = 'image/png';
            } else if (keepWebp) {
                outputType = 'image/webp';
            }

            let blob = await canvasToBlob(
                canvas,
                outputType,
                outputType === 'image/png' ? undefined : quality
            );

            if (!blob) {
                return file;
            }

            if (keepPng && blob.size >= file.size) {
                const jpegBlob = await canvasToBlob(canvas, 'image/jpeg', quality);
                if (jpegBlob && jpegBlob.size < blob.size) {
                    blob = jpegBlob;
                    outputType = 'image/jpeg';
                }
            }

            if (blob.size >= file.size) {
                return file;
            }

            const extension = outputType === 'image/png'
                ? 'png'
                : outputType === 'image/webp'
                    ? 'webp'
                    : 'jpg';

            return new File([blob], replaceExtension(file.name, extension), {
                type: outputType,
                lastModified: Date.now(),
            });
        } catch (error) {
            console.warn('Image compression skipped:', error);
            return file;
        }
    }

    async function compressImageFiles(files, options = {}) {
        const compressed = [];
        for (const file of files) {
            compressed.push(await compressImageFile(file, options));
        }
        return compressed;
    }

    window.compressImageFile = compressImageFile;
    window.compressImageFiles = compressImageFiles;
})();
