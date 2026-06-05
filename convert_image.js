import sharp from 'sharp';
import process from 'process';

const args = process.argv.slice(2);
if (args.length < 2) {
    console.error("Usage: node convert_image.js <inputPath> <outputPath>");
    process.exit(1);
}

const [inputPath, outputPath] = args;

async function run() {
    try {
        await sharp(inputPath)
            .resize({
                width: 1024,
                height: 1024,
                fit: 'inside',
                withoutEnlargement: true
            })
            .jpeg({ quality: 90 })
            .toFile(outputPath);
        console.log("SUCCESS");
        process.exit(0);
    } catch (err) {
        console.error("Conversion and resizing failed:", err);
        process.exit(1);
    }
}

run();
