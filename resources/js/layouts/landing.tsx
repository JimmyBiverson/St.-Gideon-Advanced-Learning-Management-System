import React from 'react';
import GoogleAnalytics from '@/components/google-analytics';
import MetaPixel from '@/components/meta-pixel';
import Footer from './footer';
import Main from './main';
import Navbar from './navbar';

interface LayoutProps {
   children: React.ReactNode;
   language?: boolean;
   page?: Page;
   frontend?: Project | null;
}

const LandingLayout = ({
   children,
   language = false,
   page,
   frontend,
}: LayoutProps) => {
   const lastSegment =
      typeof window !== 'undefined'
         ? window.location.href.split('/').pop()
         : (page?.slug ?? '');

   const navbarHeight =
      lastSegment === 'careers'
         ? false
         : page
           ? page.type === 'home' || page.type !== 'inner'
              ? true
              : false
           : true;

   const customizable = frontend
      ? false
      : page
        ? page.slug === 'about-us' ||
          page.slug === 'our-team' ||
          page.type !== 'inner_page'
        : false;

   return (
      <Main>
         <MetaPixel />
         <GoogleAnalytics />

         <div className="flex min-h-screen flex-col justify-between overflow-x-hidden">
            <main>
               <Navbar
                  heightCover={navbarHeight}
                  customizable={customizable}
                  language={language}
               />

               {children}
            </main>

            <Footer />
         </div>
      </Main>
   );
};

export default LandingLayout;
