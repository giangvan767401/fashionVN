import { Jimp } from 'jimp';

async function main() {
    const src = 'c:\\Users\\ASUS\\.gemini\\antigravity\\brain\\26575b3d-0c07-4d46-b0f7-24de810268db\\lumiere_logo_1772170733985.png';

    // Desktop logo
    const img1 = await Jimp.read(src);
    img1.resize({ w: 184, h: 46 });
    await img1.write('public/user/img/Logo.png');


    console.log('Images resized and saved successfully.');
}

main().catch(err => {
    console.error('Error resizing images:', err);
});
