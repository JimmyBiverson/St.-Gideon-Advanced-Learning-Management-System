import { Link, router, usePage } from '@inertiajs/react';
import type { LucideProps } from 'lucide-react';
import { LayoutDashboard, LogOut } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { TabsList } from '@/components/ui/tabs';
import { dashboard, logout } from '@/routes';
import student from '@/routes/student';

interface TabListsProps {
   onNavigate?: () => void;
   tabs: {
      id: string;
      name: string;
      slug: string;
      Icon: React.ForwardRefExoticComponent<
         Omit<LucideProps, 'ref'> & React.RefAttributes<SVGSVGElement>
      >;
   }[];
}

const TabLists = ({ tabs, onNavigate }: TabListsProps) => {
   const { props, url } = usePage<StudentDashboardProps>();
   const { auth, system, instructor, translate } = props;
   const { button, common } = translate;

   return (
      <div className="w-full min-w-0">
         <div className="mb-6 flex flex-col items-center">
            <div className="h-[120px] w-[120px] overflow-hidden rounded-full">
               <img
                  alt={`${auth.user.name}'s profile`}
                  src={auth.user.photo || '/assets/icons/avatar.png'}
                  className="h-full w-full content-center object-cover"
               />
            </div>

            <h6 className="mt-8 mb-1 font-bold">{auth.user.name}</h6>

            <p className="text-sm text-muted-foreground">{auth.user.email}</p>
         </div>

         {instructor && instructor.status === 'approved' && (
            <Link
               href={dashboard()}
               onClick={() => onNavigate?.()}
               className="mb-2 flex h-10 w-full items-center justify-start gap-3 rounded-lg px-5 py-3 text-start hover:bg-muted"
            >
               <LayoutDashboard className="h-4 w-4" />
               <span>{common.dashboard}</span>
            </Link>
         )}

         <TabsList className="grid h-auto grid-cols-1 gap-2 bg-transparent p-0">
            {tabs.map(({ id, name, slug, Icon }) => (
               <Link
                  key={id}
                  href={student.index({ tab: slug })}
                  onClick={() => onNavigate?.()}
                  className={`relative flex h-10 cursor-pointer items-center justify-start gap-3 rounded-lg px-4 text-start font-normal text-sidebar-accent-foreground/80 hover:bg-muted hover:text-sidebar-accent-foreground ${url.includes(`/student/${slug}`) ? 'bg-primary/10 text-primary hover:bg-primary/15' : ''}`}
               >
                  <Icon className="h-4 w-4" />
                  <span>{name}</span>
               </Link>
            ))}

            <Button
               variant="ghost"
               className="h-10 w-full justify-start gap-3 !px-4 font-normal text-sidebar-accent-foreground/80 hover:bg-red-100 hover:text-red-500"
               onClick={() => {
                  onNavigate?.();
                  router.post(logout());
               }}
            >
               <LogOut className="h-4 w-4" />
               <span>{button.logout}</span>
            </Button>
         </TabsList>

         {((system.sub_type === 'collaborative' && !instructor) ||
            (instructor && instructor.status !== 'approved')) && (
            <Link
               href={student.index({ tab: 'instructor' })}
               onClick={() => onNavigate?.()}
               className="mt-6 flex h-10 w-full items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium hover:bg-accent hover:text-accent-foreground"
            >
               {button.become_instructor}
            </Link>
         )}
      </div>
   );
};

export default TabLists;

// peer/menu-button flex w-full items-center overflow-hidden rounded-md p-2 text-left outline-hidden ring-sidebar-ring transition-[width,height,padding] focus-visible:ring-2 active:text-sidebar-accent-foreground disabled:pointer-events-none disabled:opacity-50 group-has-data-[sidebar=menu-action]/menu-item:pr-8 aria-disabled:pointer-events-none aria-disabled:opacity-50 data-[active=true]:bg-sidebar-accent data-[active=true]:font-medium data-[active=true]:text-sidebar-accent-foreground data-[state=open]:hover:bg-sidebar-accent data-[state=open]:hover:text-sidebar-accent-foreground group-data-[collapsible=icon]:size-8 group-data-[collapsible=icon]:p-2 [&>span:last-child]:truncate [&>svg]:size-4 [&>svg]:shrink-0 hover:text-sidebar-accent-foreground text-sm h-10 cursor-pointer gap-3 px-3 hover:bg-transparent active:bg-transparent
