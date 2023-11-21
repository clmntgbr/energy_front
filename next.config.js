/** @type {import('next').NextConfig} */
const nextConfig = {
    output: 'export',
    images: {
        domains: ['avatars.githubusercontent.com', 'avatar.vercel.sh']
    }
};

module.exports = nextConfig;
